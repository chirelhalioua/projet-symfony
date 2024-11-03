<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(
            ['isPublished' => true],
            ['eventDate' => 'ASC'],
            12,
            0
        );

        return $this->render('security/index.html.twig', ['events' => $events]);
    }
    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('security/about.html.twig');
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion()
    {
        return $this->render('security/connexion.html.twig');
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription()
    {
        return $this->render('security/inscription.html.twig');
    }
}
