<?php

namespace App\Controller;

use App\Entity\Plant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanteController extends AbstractController
{
	#[Route('/', name: 'accueil')]
	public function index(EntityManagerInterface $gestionnaire): Response
	{
		$plantes = $gestionnaire->getRepository(Plant::class)->createQueryBuilder('p')
			->where('p.stock > 0')
			->orderBy('p.name', 'ASC')
			->getQuery()
			->getResult();

		return $this->render('plante/index.html.twig', ['plants' => $plantes]);
	}

	#[Route('/plantes/{id}', name: 'plante_afficher')]
	public function show(Plant $plante): Response
	{
		return $this->render('plante/show.html.twig', ['plante' => $plante]);
	}
}
