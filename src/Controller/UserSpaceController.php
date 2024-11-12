<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSpaceController extends AbstractController
{
    /**
     * @Route("/mon-espace", name="mon_espace")
     */
    public function index(Security $security): Response
    {
        $user = $security->getUser();

        // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
        if (!$user) {
            return $this->redirectToRoute('connexion');
        }

        // Passez l'utilisateur au template
        return $this->render('security/mon_espace.html.twig', [
            'user' => $user,
        ]);
    }
}
