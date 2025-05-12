<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
	#[Route('/utilisateurs/{id}', name: 'utilisateur_afficher')]
	public function show(User $utilisateur): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		return $this->render('utilisateur/show.html.twig', ['utilisateur' => $utilisateur]);
	}

	#[Route('/utilisateurs/{id}/modifier', name: 'utilisateur_modifier')]
	public function edit(User $utilisateur): Response
	{
		$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
		return $this->render('utilisateur/edit.html.twig', ['utilisateur' => $utilisateur]);
	}
}
