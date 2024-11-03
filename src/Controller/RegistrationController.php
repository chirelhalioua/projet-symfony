<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="inscription")
     */
    public function inscription(Request $request): Response
    {
        $user = new User(); // Créez une nouvelle instance de l'utilisateur
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'utilisateur dans la base de données
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Vérifiez si l'utilisateur a été inséré
            $this->addFlash('success', 'Inscription réussie!');
            return $this->redirectToRoute('some_route'); // Changez 'some_route' par la route où vous voulez rediriger
        } else {
            // Si le formulaire n'est pas valide
            $this->addFlash('error', 'Erreur lors de l\'inscription.');
        }

        // Afficher le formulaire même si l'utilisateur n'est pas inscrit
        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
