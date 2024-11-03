<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Category;
use App\Repository\EventRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function createEvent(): Response
    {
        // on crée un événement, ces données pourraient venir d'un formulaire
        $event = new Event();
        $event->setPicture('https://images.pexels.com/photos/251225/pexels-photo-251225.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260');
        $event->setTitle('À la découverte du développement web');
        $event->setAddress('Sacré Coeur 3 VDN, Dakar');
        $event->setDescription('Lorem ipsum dolor sit amet consectetur
            adipisicing, elit. Libero tenetur beatae repellendus possimus magni
            quae! Impedit soluta sit iusto amet unde repudiandae fugit
            perspiciatis, deleniti quod placeat.');
        // la date de l'événement c'est dans 14 jours à 10h30
        $event->setEventDate((new DateTime('+14 days'))->setTime(10, 30));
        $event->setIsPublished(true); // on publie l'événement
        $event->setPublishedAt(new DateTimeImmutable());

        // on crée un deuxième événement qui ne sera pas publié pour l'instant
        $event2 = new Event();
        // on renseigne seulement le titre qui est obligatoire
        $event2->setTitle('Événement à venir, pas encore publique');

        // on ajoute quelques tags à l'événement
        $webTag = new Category();
        $webTag->setName('web');
        $event->addCategory($webTag);

        $codeTag = new Category();
        $codeTag->setName('code');
        $event->addCategory($codeTag);

        /* on récupère le gestionnaire d'entités qui va nous permettre
            d'enregistrer l'événement */
        $entityManager = $this->getDoctrine()->getEntityManager();

        /* on confie l'objet $event au gestionnaire d'entités,
            l'objet n'est pas encore enregistrer en base de données */
        $entityManager->persist($event);

        // on confie aussi l'objet $event2 au gestionnaire d'entités
        $entityManager->persist($event2);

        /* on exécute maintenant les 2 requêtes qui vont ajouter
            les objets $event et $event2 en base de données */
        $entityManager->flush();

        return new Response(
            "Les événements {$event->getTitle()} et {$event2->getTitle()}
                ont bien été enregistrés."
        );
    }

    /**
     * @Route("/events/{id}/update", name="update_event")
     */
    public function update(
        Event $event,
        EntityManagerInterface $entityManager
    ): Response {
        // grâce au ParamConverter, nous avons automatiquement accès à l'objet $event
        $event->setTitle("À la découverte du Web 2.0");
        $event->setEventDate((new \DateTime('+14 days'))->setTime(15, 30));

        $entityManager->flush();

        return new Response("L'événement a bien été modifier.");
    }

    /**
     * @Route("/events/{id}/delete", name="delete_event")
     */
    public function delete(
        Event $event,
        EntityManagerInterface $entityManager
    ): Response {
        // grâce au ParamConverter, nous avons automatiquement accès à l'objet $event
        $entityManager->remove($event); // on utilise la method remove de l'entity manager
        $entityManager->flush();

        return new Response("L'événement {$event->getId()} à bien été supprimer.");
    }


        /**
         * @Route("/events", name="list_events")
         */
        public function list(EventRepository $eventRepository): Response
        {
            // Récupération de tous les événements
            $events = $eventRepository->findAll();
    
            return $this->render('event/list.html.twig', [
                'events' => $events,
            ]);
        }
    
        /**
         * @Route("/events/category/{category}", name="list_events_by_category")
         */
        public function listByCategory($category = null): Response
        {
            // Logique pour lister les événements par catégorie
            // Vous pouvez implémenter la logique ici si nécessaire
    
            $htmlMessage = "<h1>Liste des événements";
            if ($category) {
                $htmlMessage .= " avec la catégorie: $category";
            }
            $htmlMessage .= "</h1>";
    
            return new Response($htmlMessage);
        }
    }
    