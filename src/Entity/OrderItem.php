<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'articles')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Order $commande = null;

	#[ORM\ManyToOne(inversedBy: 'articles')]
	#[ORM\JoinColumn(name: 'plant_id', referencedColumnName: 'id', nullable: false)]
	private ?Plant $plant = null;

	#[ORM\Column(type: 'integer')]
	private int $quantity;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getCommande(): ?Order
	{
		return $this->commande;
	}

	public function setCommande(?Order $order): self
	{
		$this->commande = $order;
		return $this;
	}

	public function getPlant(): ?Plant
	{
			return $this->plant;
	}

	public function setPlant(?Plant $plant): static
	{
			$this->plant = $plant;
			return $this;
	}

	public function getQuantity(): int
	{
		return $this->quantity;
	}

	public function setQuantity(int $qty): self
	{
		$this->quantity = $qty;
		return $this;
	}
}
