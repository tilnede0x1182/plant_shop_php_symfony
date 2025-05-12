<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
class Order
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'commandes')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $utilisateur = null;

	#[ORM\Column(type: 'integer')]
	private int $totalPrice = 0;

	#[ORM\Column(type: 'string', length: 100)]
	private string $status;

	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $createdAt;

	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $updatedAt;

	#[ORM\OneToMany(mappedBy: 'commande', targetEntity: OrderItem::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
	private Collection $articles;

	public function __construct()
	{
		$this->articles = new ArrayCollection();
		$this->createdAt = new \DateTime();
		$this->updatedAt = new \DateTime();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUtilisateur(): ?User
	{
		return $this->utilisateur;
	}

	public function setUtilisateur(?User $user): self
	{
		$this->utilisateur = $user;
		return $this;
	}

	public function getTotalPrice(): int
	{
		return $this->totalPrice;
	}

	public function setTotalPrice(int $value): self
	{
		$this->totalPrice = $value;
		return $this;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;
		return $this;
	}

	public function getCreatedAt(): \DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $dt): self
	{
		$this->createdAt = $dt;
		return $this;
	}

	public function getUpdatedAt(): \DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(\DateTimeInterface $dt): self
	{
		$this->updatedAt = $dt;
		return $this;
	}

	/**
	 * @return Collection<int, OrderItem>
	 */
	public function getArticles(): Collection
	{
		return $this->articles;
	}

	public function addArticle(OrderItem $item): self
	{
		if (!$this->articles->contains($item)) {
			$this->articles[] = $item;
			$item->setCommande($this);
		}
		return $this;
	}

	public function removeArticle(OrderItem $item): self
	{
		if ($this->articles->removeElement($item)) {
			if ($item->getCommande() === $this) {
				$item->setCommande(null);
			}
		}
		return $this;
	}
}
