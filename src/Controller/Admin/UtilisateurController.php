<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs')]
class UtilisateurController extends AbstractController
{
	#[Route('', name: 'admin_utilisateurs_index')]
	public function index(EntityManagerInterface $em): Response
	{
		$users = $em->getRepository(User::class)->findAll();
		return $this->render('admin/utilisateur/index.html.twig', ['users' => $users]);
	}

	#[Route('/nouveau', name: 'admin_users_create')]
	public function create(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
	{
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$plainPassword = $form->get('plainPassword')->getData();
			if ($plainPassword) {
				$user->setPassword($hasher->hashPassword($user, $plainPassword));
			}
			$em->persist($user);
			$em->flush();
			return $this->redirectToRoute('admin_utilisateurs_index');
		}

		return $this->render('admin/utilisateur/form.html.twig', [
			'userForm' => $form->createView()
		]);
	}

	#[Route('/{id}', name: 'admin_users_show', methods: ['GET'])]
	public function show(User $user): Response
	{
		return $this->render('admin/utilisateur/show.html.twig', [
			'user' => $user
		]);
	}

	#[Route('/{id}/modifier', name: 'admin_users_edit')]
	public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
	{
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$plainPassword = $form->get('plainPassword')->getData();
			if ($plainPassword) {
				$user->setPassword($hasher->hashPassword($user, $plainPassword));
			}
			$em->flush();
			return $this->redirectToRoute('admin_utilisateurs_index');
		}

		return $this->render('admin/utilisateur/form.html.twig', [
			'userForm' => $form->createView(),
			'user' => $user
		]);
	}

	#[Route('/{id}/supprimer', name: 'admin_users_delete', methods: ['POST'])]
	public function delete(Request $request, User $user, EntityManagerInterface $em): Response
	{
		if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
			$em->remove($user);
			$em->flush();
		}
		return $this->redirectToRoute('admin_utilisateurs_index');
	}
}
