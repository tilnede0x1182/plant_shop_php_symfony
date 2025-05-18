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
		$plantes = $gestionnaire->getRepository(Plant::class)->findBy([], ['name' => 'ASC']);
		return $this->render('admin/plante/index.html.twig', ['plantes' => $plantes]);
	}

	#[Route('/nouvelle', name: 'admin_plante_new')]
	public function create(Request $request, EntityManagerInterface $em): Response
	{
		$plante = new Plant();
		$form = $this->createForm(PlanteType::class, $plante);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->persist($plante);
			$em->flush();
			$this->addFlash('success', 'Plante ajoutée avec succès.');
			return $this->redirectToRoute('admin_plantes_index');
		}
		return $this->render('admin/plante/new.html.twig', ['form' => $form->createView()]);
	}

	#[Route('/{id}/modifier', name: 'admin_plante_edit')]
	public function edit(Plant $plant, Request $request, EntityManagerInterface $em): Response
	{
		$form = $this->createForm(PlanteType::class, $plant);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();
			$this->addFlash('success', 'Plante modifiée avec succès.');
			return $this->redirectToRoute('admin_plantes_index');
		}
		return $this->render('admin/plante/edit.html.twig', ['form' => $form->createView(), 'plant' => $plant]);
	}

	#[Route('/{id}', name: 'admin_plante_delete', methods: ['POST'])]
	public function delete(Request $request, Plant $plant, EntityManagerInterface $em): Response
	{
		if ($this->isCsrfTokenValid('delete' . $plant->getId(), $request->request->get('_token'))) {
			$em->remove($plant);
			$em->flush();
		}
		$this->addFlash('success', 'Plante supprimée.');
		return $this->redirectToRoute('admin_plantes_index');
	}
}
