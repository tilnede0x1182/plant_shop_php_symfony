<?php

namespace App\Controller;

use App\Entity\Plante;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanteController extends AbstractController
{
	#[Route('/', name: 'accueil')]
	public function index(EntityManagerInterface $gestionnaire): Response
	{
		$plantes = $gestionnaire->getRepository(Plante::class)->findBy(['stock' => 0], ['name' => 'ASC']);
		return $this->render('plante/index.html.twig', ['plantes' => $plantes]);
	}

	#[Route('/plantes/{id}', name: 'plante_afficher')]
	public function show(Plante $plante): Response
	{
		return $this->render('plante/show.html.twig', ['plante' => $plante]);
	}
}
