<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UtilisateurController extends AbstractController
{
    #[Route('/utilisateurs/{id}', name: 'utilisateur_afficher')]
    public function show(User $utilisateur): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    #[Route('/utilisateurs/{id}/modifier', name: 'utilisateur_modifier')]
    public function edit(
        Request $request,
        User $utilisateur,
        EntityManagerInterface $em,
        UserAuthenticatorInterface $authenticator,
        LoginFormAuthenticator $loginAuthenticator
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UserType::class, $utilisateur, [
            'validation_groups' => ['Default'],
        ]);
				$form->remove('admin');
        $form->remove('plainPassword');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on persiste (entité déjà managed) et flush
            $em->persist($utilisateur);
            $em->flush();

            // réauthentification si c'est l'utilisateur connecté
            if ($utilisateur === $this->getUser()) {
                $response = $authenticator->authenticateUser(
                    $utilisateur,
                    $loginAuthenticator,
                    $request
                );
                if ($response instanceof Response) {
                    return $response;
                }
            }

            return $this->redirectToRoute('accueil');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'userForm'    => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }
}
