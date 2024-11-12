<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Entity\Category;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
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
        $event->setEventDate((new \DateTime('+14 days'))->setTime(10, 30));
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
        $event->setEventDate((new \DateTime('+14 days'))->setTime(15, 30));

        $entityManager->flush();

        // Rediriger vers l'événement mis à jour
        return $this->redirectToRoute('show_event', ['id' => $event->getId()]);
    }

    /**
     * @Route("/events/{id}/delete", name="delete_event")
     */
    public function delete(Event $event, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si l'événement a des utilisateurs associés et les détacher avant de supprimer
        $event->getUsers()->clear(); // Retirer les utilisateurs associés si nécessaire

        $entityManager->remove($event);
        $entityManager->flush();

        // Retourner une réponse de succès
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
    public function listByCategory(string $category = null, EventRepository $eventRepository, CategoryRepository $categoryRepository): Response
    {
        if ($category) {
            $categoryEntity = $categoryRepository->findOneBy(['name' => $category]);
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
            return $this->redirectToRoute('login');
        }

        // Récupérer les événements réservés par l'utilisateur
        $reservedEvents = $user->getReservations();

        // Debug : Vérifier les réservations
        dump($reservedEvents); // Cela va afficher le contenu de reservedEvents dans le profiler

        // Passer les réservations à la vue
        return $this->render('security/mon_espace.html.twig', [
            'user' => $user,
            'reservedEvents' => $reservedEvents,  // Passez la variable réservations ici
        ]);
    }



    /**
     * @Route("/events/{eventId}/add-user/{userId}", name="add_user_to_event")
     */
    public function addUserToEvent(int $eventId, int $userId, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'événement et l'utilisateur via le repository
        $event = $entityManager->getRepository(Event::class)->find($eventId);
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$event || !$user) {
            throw $this->createNotFoundException("Événement ou utilisateur non trouvé.");
        }

        // Ajouter l'utilisateur à la relation "users" de l'événement
        if (!$event->getUsers()->contains($user)) {
            $event->addUser($user);  // Cette méthode doit exister dans votre entité Event
            $entityManager->flush();
        }

        // Rediriger vers la page de l'événement
        return $this->redirectToRoute('show_event', ['id' => $eventId]);
    }

    public function reserveEvent($id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Récupère l'utilisateur authentifié

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        // Récupérer l'événement par son ID
        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }

        // Vérifier si l'événement n'est pas déjà réservé
        if (!$user->getReservation()->contains($event)) {
            $user->addReservation($event);  
            $entityManager->flush();
        }

    // Rediriger vers l'espace personnel
    return $this->redirectToRoute('mon_espace');
}

    /**
     * @Route("/events/{id}/cancel", name="cancel_reservation")
     */
    public function cancelReservation($id, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $event = $entityManager->getRepository(Event::class)->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement non trouvé.');
        }

        // Retirer l'événement des réservations de l'utilisateur
        $user->removeReservation($event);
        $entityManager->flush();

        // Rediriger vers l'espace personnel
        return $this->redirectToRoute('mon_espace');
    }
}
