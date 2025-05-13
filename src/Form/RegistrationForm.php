<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationForm extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Nom'
			])
			->add('email', EmailType::class, [
				'label' => 'Email'
			])
			->add('plainPassword', PasswordType::class, [
				'mapped' => false,
				'label' => 'Mot de passe',
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank([
						'message' => 'Entrez un mot de passe',
					]),
					new Length([
						'min' => 6,
						'minMessage' => 'Mot de passe minimum : {{ limit }} caractères',
						'max' => 4096,
					]),
				],
			])
			->add('confirmPassword', PasswordType::class, [
				'mapped' => false,
				'label' => 'Confirmation',
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank([
						'message' => 'Confirmez le mot de passe',
					]),
				],
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
