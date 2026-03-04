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
	//  Variables globales pour la seed
	const NB_ADMINS = 3;  // 👤 Nombre d'administrateurs à créer
	const NB_USERS = 20;  // 👥 Nombre d'utilisateurs à créer
	const NB_PLANTES = 30; // 🌱 Nombre de plantes à créer

	private EntityManagerInterface $em;
	private UserPasswordHasherInterface $hasher;

	// 60 noms réels de plantes, noms scientifiques entre parenthèses
	private array $nomsPlantes = [
		"Rose", "Tulipe", "Lavande", "Orchidée", "Basilic", "Menthe", "Pivoine", "Tournesol",
		"Cactus (Echinopsis)", "Bambou", "Camomille (Matricaria recutita)", "Sauge (Salvia officinalis)",
		"Romarin (Rosmarinus officinalis)", "Thym (Thymus vulgaris)", "Laurier-rose (Nerium oleander)",
		"Aloe vera", "Jasmin (Jasminum officinale)", "Hortensia (Hydrangea macrophylla)",
		"Marguerite (Leucanthemum vulgare)", "Géranium (Pelargonium graveolens)", "Fuchsia (Fuchsia magellanica)",
		"Anémone (Anemone coronaria)", "Azalée (Rhododendron simsii)", "Chrysanthème (Chrysanthemum morifolium)",
		"Digitale pourpre (Digitalis purpurea)", "Glaïeul (Gladiolus hortulanus)", "Lys (Lilium candidum)",
		"Violette (Viola odorata)", "Muguet (Convallaria majalis)", "Iris (Iris germanica)",
		"Lavandin (Lavandula intermedia)", "Érable du Japon (Acer palmatum)", "Citronnelle (Cymbopogon citratus)",
		"Pin parasol (Pinus pinea)", "Cyprès (Cupressus sempervirens)", "Olivier (Olea europaea)",
		"Papyrus (Cyperus papyrus)", "Figuier (Ficus carica)", "Eucalyptus (Eucalyptus globulus)",
		"Acacia (Acacia dealbata)", "Bégonia (Begonia semperflorens)", "Calathea (Calathea ornata)",
		"Dieffenbachia (Dieffenbachia seguine)", "Ficus elastica", "Sansevieria (Sansevieria trifasciata)",
		"Philodendron (Philodendron scandens)", "Yucca (Yucca elephantipes)", "Zamioculcas zamiifolia",
		"Monstera deliciosa", "Pothos (Epipremnum aureum)", "Agave (Agave americana)", "Cactus raquette (Opuntia ficus-indica)",
		"Palmier-dattier (Phoenix dactylifera)", "Amaryllis (Hippeastrum hybridum)", "Bleuet (Centaurea cyanus)",
		"Cœur-de-Marie (Lamprocapnos spectabilis)", "Croton (Codiaeum variegatum)", "Dracaena (Dracaena marginata)",
		"Hosta (Hosta plantaginea)", "Lierre (Hedera helix)", "Mimosa (Acacia dealbata)"
	];

	/**
	 * Constructeur du command de seed.
	 *
	 * @param EntityManagerInterface $em Gestionnaire d'entités
	 * @param UserPasswordHasherInterface $hasher Hasheur de mot de passe
	 */
	public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher)
	{
		parent::__construct();
		$this->em = $em;
		$this->hasher = $hasher;
	}

	/**
	 * Exécute la commande de seed.
	 *
	 * @param InputInterface $input Interface d'entrée
	 * @param OutputInterface $output Interface de sortie
	 * @return int Code de retour
	 */
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

	/**
	 * Réinitialise la base de données.
	 *
	 * @return void
	 */
	private function resetDatabase(): void
	{
		$this->em->getConnection()->executeQuery('SET session_replication_role = replica');
		$this->em->createQuery('DELETE FROM App\Entity\OrderItem')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\Order')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\Plant')->execute();
		$this->em->createQuery('DELETE FROM App\Entity\User')->execute();
		$this->em->getConnection()->executeQuery('SET session_replication_role = DEFAULT');
	}

	/**
	 * Crée les utilisateurs de test.
	 *
	 * @param Generator $faker Générateur Faker
	 * @param SymfonyStyle $io Interface de sortie stylée
	 * @return array Liste des utilisateurs créés
	 */
	private function seedUsers(Generator $faker, SymfonyStyle $io): array
	{
		$users = [];

		$io->section('Création des administrateurs');
		for ($i = 1; $i <= self::NB_ADMINS; $i++) {
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
		for ($i = 1; $i <= self::NB_USERS; $i++) {
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

	/**
	 * Retourne le nom de la plante selon la logique Ruby/Rails
	 * @param int $iterator Index de création (commence à 0)
	 * @return string
	 */
	private function getPlantName(int $iterator): string
	{
		$noms = $this->nomsPlantes;
		$taille = count($noms);
		if (self::NB_PLANTES > $taille) {
			return $noms[$iterator % $taille] . ' ' . (intdiv($iterator, $taille) + 1);
		}
		return $noms[$iterator % $taille];
	}

	/**
	 * Crée les plantes de test.
	 *
	 * @param Generator $faker Générateur Faker
	 * @return array Liste des plantes créées
	 */
	private function seedPlants(Generator $faker): array
	{
		$plants = [];
		for ($iterator = 0; $iterator < self::NB_PLANTES; $iterator++) {
			$plant = new Plant();
			$plant->setName($this->getPlantName($iterator));
			$plant->setDescription($faker->sentence(10));
			$plant->setPrice(rand(5, 50));
			$plant->setStock(rand(5, 30));
			$this->em->persist($plant);
			$plants[] = $plant;
		}
		return $plants;
	}

	/**
	 * Sauvegarde les identifiants des utilisateurs dans un fichier.
	 *
	 * @param array $users Liste des utilisateurs
	 * @return void
	 */
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
