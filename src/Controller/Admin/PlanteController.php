<?php

namespace App\Controller\Admin;

use App\Entity\Plant;
use App\Form\PlanteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/plantes')]
class PlanteController extends AbstractController
{
	#[Route('', name: 'admin_plantes_index')]
	public function index(EntityManagerInterface $gestionnaire): Response
	{
		$plantes = $gestionnaire->getRepository(Plant::class)->findAll();
		return $this->render('admin/plante/index.html.twig', ['plantes' => $plantes]);
	}
}
