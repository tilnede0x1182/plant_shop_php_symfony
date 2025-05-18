<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use Psr\Log\LoggerInterface;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
	#[Route('/commandes', name: 'commandes_index')]
	public function index(): Response
	{
		/** @var \App\Entity\User $utilisateur */
		$utilisateur = $this->getUser();
		$commandes = $utilisateur->getCommandes()->toArray();
		usort($commandes, fn($a, $b) => $b->getCreatedAt() <=> $a->getCreatedAt());

		return $this->render('commande/index.html.twig', ['orders' => $commandes]);
	}

	#[Route('/commandes/nouvelle', name: 'commande_creer')]
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	public function create(): Response
	{
		return $this->render('commande/new.html.twig');
	}

	#[Route('/commandes/enregistrer', name: 'commande_enregistrer', methods: ['POST'])]
	public function store(Request $requete, EntityManagerInterface $gestionnaire, PlantRepository $repo, LoggerInterface $logger): Response
	{
		$logger->info('CommandeController::store appelé', [
			'request_items' => $requete->get('items')
		]);
		$donnees = json_decode($requete->get('items'), true);
		$logger->info('Items reçus : ' . print_r($donnees, true));
		$total = 0;

		$commande = new Order();
		$commande->setUtilisateur($this->getUser());
		$commande->setStatus('confirmed');
		$commande->setTotalPrice(0); // Initialisation

		$gestionnaire->persist($commande); // Persist AVANT la création des items

		if (empty($donnees)) {
			return new Response('Aucun item reçu ou JSON malformé : ' . $requete->get('items'), 400);
		}
		foreach ($donnees as $ligne) {
			$plante = $repo->find($ligne['plant_id']);
			if (!$plante) {
				$logger->error("Plante ID {$ligne['plant_id']} introuvable.");
				continue;
			}
			if ($plante->getStock() < $ligne['quantity']) {
				return $this->redirectToRoute('commande_creer', [], Response::HTTP_SEE_OTHER);
			}
			$plante->setStock($plante->getStock() - $ligne['quantity']);
			$item = new OrderItem();
			$item->setPlant($plante);
			$item->setQuantity($ligne['quantity']);
			$item->setCommande($commande);
			$total += $plante->getPrice() * $ligne['quantity'];
			$gestionnaire->persist($item);
		}
		$commande->setTotalPrice($total); // Mise à jour du total calculé
		$gestionnaire->flush();
		$requete->getSession()->remove('panier');
		$this->addFlash('success', 'Commande enregistrée avec succès.');
		return $this->redirectToRoute('commandes_index', ['success' => true]);
	}
}
