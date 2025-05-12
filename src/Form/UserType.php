<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
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
			->add('admin', CheckboxType::class, [
				'label' => 'Administrateur',
				'required' => false
			])
			->add('plainPassword', PasswordType::class, [
				'mapped' => false,
				'required' => false,
				'label' => 'Mot de passe',
				'attr' => ['autocomplete' => 'new-password'],
				'constraints' => [
					new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
					new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit faire au moins {{ limit }} caractères'])
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => User::class,
		]);
	}
}
