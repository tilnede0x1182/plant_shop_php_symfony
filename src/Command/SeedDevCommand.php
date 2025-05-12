<?php

namespace App\Command;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Plant;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:seed:dev', description: 'Remplit la base de données de dev avec des données')]
class SeedDevCommand extends Command
{
	private EntityManagerInterface $em;
	private UserPasswordHasherInterface $hasher;

	public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
	{
		parent::__construct();
		$this->em = $em;
		$this->hasher = $hasher;
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$faker = Factory::create('fr_FR');

		$this->resetDatabase();
		$users = $this->seedUsers($faker, $io);
		$plants = $this->seedPlants($faker);
		$this->em->flush();
		$io->success('Base de données peuplée avec succès.');
		$this->saveUserCredentialsFile($users);

		return Command::SUCCESS;
	}

	private function resetDatabase(): void
	{
		$this->em->getConnection()->executeQuery('SET session_replication_role = replica');
		$this->em->createQuery('DELETE FROM App\Entity\OrderItem')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\Order')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\Plant')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\User')->execute();
		$this->em->getConnection()->executeQuery('SET session_replication_role = DEFAULT');
	}

	private function seedUsers(Generator $faker, SymfonyStyle $io): array
	{
		$users = [];

		$io->section('Création des administrateurs');
		for ($i = 1; $i <= 3; $i++) {
			$user = new User();
			$user->setName($faker->name());
			$user->setEmail("admin{$i}@planteshop.com");
			$user->setPassword($this->hasher->hashPassword($user, 'password'));
			$user->setAdmin(true);
			$this->em->persist($user);
			$users[] = $user;
			$io->text("admin{$i}@planteshop.com / password");
		}

		$io->section('Création des utilisateurs');
		for ($i = 1; $i <= 15; $i++) {
			$user = new User();
			$user->setName($faker->name());
			$user->setEmail($faker->unique()->safeEmail());
			$user->setPassword($this->hasher->hashPassword($user, 'password'));
			$user->setAdmin(false);
			$this->em->persist($user);
			$users[] = $user;
			$io->text($user->getEmail() . ' / password');
		}

		return $users;
	}

	private function seedPlants(Generator $faker): array
	{
		$noms = ['Rose', 'Tulipe', 'Lavande', 'Orchidée', 'Basilic', 'Menthe', 'Pivoine', 'Tournesol', 'Cactus', 'Bambou'];
		$plants = [];

		foreach (range(1, 30) as $i) {
			$plant = new Plant();
			$plant->setName($noms[$i % count($noms)] . " $i");
			$plant->setDescription($faker->sentence(10));
			$plant->setPrice(rand(5, 50));
			$plant->setStock(rand(5, 30));
			$this->em->persist($plant);
			$plants[] = $plant;
		}

		return $plants;
	}

	private function saveUserCredentialsFile(array $users): void
	{
		$content = "=== ADMINS ===\n";
		foreach ($users as $user) {
			if ($user->isAdmin()) {
				$content .= $user->getEmail() . " password\n";
			}
		}

		$content .= "\n=== USERS ===\n";
		foreach ($users as $user) {
			if (!$user->isAdmin()) {
				$content .= $user->getEmail() . " password\n";
			}
		}

		file_put_contents(__DIR__ . '/../../users.txt', $content);
	}
}
