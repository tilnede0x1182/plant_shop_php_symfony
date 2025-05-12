<?php

namespace App\Form;

use App\Entity\Plant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanteType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('email')
			->add('name', TextType::class, [
				'label' => 'Nom'
			])
			->add('price', IntegerType::class, [
				'label' => 'Prix (€)'
			])
			->add('description', TextareaType::class, [
				'label' => 'Description',
				'required' => false
			])
			->add('stock', IntegerType::class, [
				'label' => 'Stock'
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Plant::class,
		]);
	}
}
