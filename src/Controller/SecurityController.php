<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(EventRepository $eventRepository): Response
    {
        // Obtenir les événements publiés, triés par date d'événement
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
    public function about(): Response
    {
        return $this->render('security/about.html.twig');
    }

    /**
     * @Route("/connexion", name="connexion")
     */
    public function connexion(AuthenticationUtils $authenticationUtils): Response
    {
        // Obtenir les erreurs de connexion (le cas échéant)
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($error) {
            $this->addFlash('error', 'Identifiants invalides pour l\'utilisateur : ' . $lastUsername);
        }

        return $this->render('security/connexion.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
 * @Route("/mon-espace", name="mon_espace")
 */
    public function monEspace(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('connexion');
        }

        return $this->render('security/mon_espace.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(): Response
    {
        return $this->render('security/inscription.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        // Ce code ne sera jamais exécuté car Symfony gère la déconnexion automatiquement
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
