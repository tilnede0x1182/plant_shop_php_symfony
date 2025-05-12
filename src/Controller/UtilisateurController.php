<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
	#[Route('/utilisateurs/{id}', name: 'utilisateur_afficher')]
	public function show(Utilisateur $utilisateur): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');
		return $this->render('utilisateur/show.html.twig', ['utilisateur' => $utilisateur]);
	}

	#[Route('/utilisateurs/{id}/modifier', name: 'utilisateur_modifier')]
	public function edit(Utilisateur $utilisateur): Response
	{
		$this->denyAccessUnlessGranted('ROLE_USER');
		return $this->render('utilisateur/edit.html.twig', ['utilisateur' => $utilisateur]);
	}
}
