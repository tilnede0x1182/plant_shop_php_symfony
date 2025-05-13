<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
	#[Route('/register', name: 'app_register')]
	public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
	{
		$user = new User();
		$form = $this->createForm(RegistrationForm::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted()) {
			$plainPassword = $form->get('plainPassword')->getData();
			$confirmPassword = $form->get('confirmPassword')->getData();

			if ($plainPassword !== $confirmPassword) {
				$form->get('confirmPassword')->addError(new \Symfony\Component\Form\FormError('Les mots de passe ne correspondent pas.'));
			} elseif ($form->isValid()) {
				$user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
				$entityManager->persist($user);
				$entityManager->flush();

				return $security->login($user, LoginFormAuthenticator::class, 'main');
			}
		}

		return $this->render('registration/register.html.twig', [
			'registerForm' => $form,
		]);
	}
}
