<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeItem;
use App\Entity\Plante;
use App\Repository\PlanteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
	#[Route('/commandes', name: 'commandes_index')]
	public function index(): Response
	{
		$utilisateur = $this->getUser();
		$commandes = $utilisateur->getCommandes();

		return $this->render('commande/index.html.twig', ['orders' => $commandes]);
	}

	#[Route('/commandes/nouvelle', name: 'commande_creer')]
	public function create(): Response
	{
		return $this->render('commande/new.html.twig');
	}

	#[Route('/commandes', name: 'commande_enregistrer', methods: ['POST'])]
	public function store(Request $requete, EntityManagerInterface $gestionnaire, PlanteRepository $repo): Response
	{
		$donnees = json_decode($requete->get('items'), true);
		$total = 0;

		$commande = new Commande();
		$commande->setUtilisateur($this->getUser());
		$commande->setStatut('confirmed');

		foreach ($donnees as $ligne) {
			$plante = $repo->find($ligne['plant_id']);
			if ($plante->getStock() < $ligne['quantity']) {
				return $this->redirectToRoute('commande_creer', [], Response::HTTP_SEE_OTHER);
			}
			$plante->setStock($plante->getStock() - $ligne['quantity']);
			$item = new CommandeItem();
			$item->setPlante($plante);
			$item->setQuantite($ligne['quantity']);
			$item->setCommande($commande);
			$total += $plante->getPrix() * $ligne['quantity'];
			$gestionnaire->persist($item);
		}
		$commande->setTotal($total);
		$gestionnaire->persist($commande);
		$gestionnaire->flush();

		return $this->redirectToRoute('commande_creer', ['success' => true]);
	}
}
