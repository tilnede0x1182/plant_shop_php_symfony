<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs')]
class UtilisateurController extends AbstractController
{
	#[Route('', name: 'admin_utilisateurs_index')]
	public function index(): Response
	{
		// À implémenter
		return $this->render('admin/utilisateur/index.html.twig');
	}
}
