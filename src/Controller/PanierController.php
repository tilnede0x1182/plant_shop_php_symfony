<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
	#[Route('/panier', name: 'panier_index')]
	public function index(): Response
	{
		return $this->render('panier/index.html.twig');
	}
}
