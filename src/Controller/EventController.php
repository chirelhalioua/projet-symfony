<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\EventRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/events/{id}", name="show_event", requirements={"id"="\d+"})
     */
    public function show($id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException(
                "L'événement avec l'id = {$id} n'existe pas!"
            );
        }

        return $this->render('event/show.html.twig', ['event' => $event]);
    }

    /**
     * @Route("/events/create", name="create_event")
     */
    public function createEvent(EntityManagerInterface $entityManager): Response
    {
        // Créer un événement
        $event = new Event();
        $event->setPicture('https://images.pexels.com/photos/251225/pexels-photo-251225.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');
        $event->setTitle('À la découverte du développement web');
        $event->setAddress('Sacré Coeur 3 VDN, Dakar');
        $event->setDescription('Lorem ipsum dolor sit amet consectetur adipisicing, elit. Libero tenetur beatae repellendus possimus magni quae! Impedit soluta sit iusto amet unde repudiandae fugit perspiciatis, deleniti quod placeat.');
        $event->setEventDate((new DateTime('+14 days'))->setTime(10, 30));
        $event->setIsPublished(true);
        $event->setPublishedAt(new DateTimeImmutable());

        // Créer des catégories et les persister
        $webTag = new Category();
        $webTag->setName('web');
        $entityManager->persist($webTag);
        $event->addCategory($webTag);

        $codeTag = new Category();
        $codeTag->setName('code');
        $entityManager->persist($codeTag);
        $event->addCategory($codeTag);

        // Persister l'événement
        $entityManager->persist($event);
        $entityManager->flush();

        // Rediriger vers la page de l'événement créé
        return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
    }

    /**
     * @Route("/events/{id}/update", name="update_event")
     */
    public function update(Event $event, EntityManagerInterface $entityManager): Response
    {
        $event->setTitle("À la découverte du Web 2.0");
        $event->setEventDate((new DateTime('+14 days'))->setTime(15, 30));

        $entityManager->flush();

        return new Response("L'événement a bien été modifié.");
    }

    /**
     * @Route("/events/{id}/delete", name="delete_event")
     */
    public function delete(Event $event, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($event);
        $entityManager->flush();

        return new Response("L'événement {$event->getId()} a bien été supprimé.");
    }

    /**
     * @Route("/events", name="list_events")
     */
    public function list(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/events/category/{category}", name="list_events_by_category")
     */
    public function listByCategory(string $category = null, EventRepository $eventRepository): Response
    {
        if ($category) {
            $categoryEntity = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['name' => $category]);
            if ($categoryEntity) {
                $events = $eventRepository->findBy(['categories' => $categoryEntity]);
            } else {
                $events = [];
            }
        } else {
            $events = [];
        }

        return $this->render('event/list_by_category.html.twig', [
            'events' => $events,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/mon-espace", name="mon_espace")
     */
    public function monEspace(Request $request): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur authentifié

        // Vérifiez que l'utilisateur est connecté
        if (!$user) {
            // Rediriger ou afficher un message d'erreur si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('login');
        }

        // Code supplémentaire pour récupérer d'autres données, par exemple les réservations
        $reservedEventId = $request->query->get('reservedEventId');
        $reservedEvent = null;

        if ($reservedEventId) {
            $reservedEvent = $this->getDoctrine()->getRepository(Event::class)->find($reservedEventId);

            if ($reservedEvent && !$user->getReservations()->contains($reservedEvent)) {
                // Ajoute l'événement aux réservations si pas encore présent
                $user->addReservation($reservedEvent);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        // Transmet l'utilisateur au template
        return $this->render('security/mon_espace.html.twig', [
            'user' => $user,
            'reservedEvent' => $reservedEvent,  // Optionnel si vous avez un événement réservé
        ]);
    }
}
