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
	/**
	 * Affiche la liste des utilisateurs (admin).
	 *
	 * @param EntityManagerInterface $em Gestionnaire d'entités
	 * @return Response
	 */
	#[Route('', name: 'admin_utilisateurs_index')]
	public function index(EntityManagerInterface $em): Response
	{
		$users = $em->getRepository(User::class)->findBy([], ['admin' => 'DESC', 'name' => 'ASC']);
		return $this->render('admin/utilisateur/index.html.twig', ['users' => $users]);
	}

	/**
	 * Affiche le formulaire de création d'un utilisateur.
	 *
	 * @param Request $request Requête HTTP
	 * @param EntityManagerInterface $em Gestionnaire d'entités
	 * @param UserPasswordHasherInterface $hasher Hasheur de mot de passe
	 * @return Response
	 */
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
			$this->addFlash('success', 'Utilisateur créé avec succès.');
			return $this->redirectToRoute('admin_utilisateurs_index');
		}

		return $this->render('admin/utilisateur/new.html.twig', [
			'userForm' => $form->createView()
		]);
	}

	/**
	 * Affiche le détail d'un utilisateur.
	 *
	 * @param User $user Utilisateur à afficher
	 * @return Response
	 */
	#[Route('/{id}', name: 'admin_users_show', methods: ['GET'])]
	public function show(User $user): Response
	{
		return $this->render('admin/utilisateur/show.html.twig', [
			'user' => $user
		]);
	}

	/**
	 * Affiche le formulaire d'édition d'un utilisateur.
	 *
	 * @param User $user Utilisateur à éditer
	 * @param Request $request Requête HTTP
	 * @param EntityManagerInterface $em Gestionnaire d'entités
	 * @param UserPasswordHasherInterface $hasher Hasheur de mot de passe
	 * @return Response
	 */
	#[Route('/{id}/modifier', name: 'admin_users_edit')]
	public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
	{
		$form = $this->createForm(UserType::class, $user);
		$form->remove('plainPassword');
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();
			$this->addFlash('success', 'Utilisateur modifié avec succès.');
			return $this->redirectToRoute('admin_utilisateurs_index');
		}

		return $this->render('admin/utilisateur/edit.html.twig', [
			'userForm' => $form->createView(),
			'user' => $user
		]);
	}

	/**
	 * Supprime un utilisateur.
	 *
	 * @param Request $request Requête HTTP
	 * @param User $user Utilisateur à supprimer
	 * @param EntityManagerInterface $em Gestionnaire d'entités
	 * @return Response
	 */
	#[Route('/{id}/supprimer', name: 'admin_users_delete', methods: ['POST'])]
	public function delete(Request $request, User $user, EntityManagerInterface $em): Response
	{
		if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
			$em->remove($user);
			$em->flush();
		}
		$this->addFlash('success', 'Utilisateur supprimé.');
		return $this->redirectToRoute('admin_utilisateurs_index');
	}
}
