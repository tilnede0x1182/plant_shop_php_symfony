<?php

namespace App\Entity;

use App\Repository\PlantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlantRepository::class)]
class Plant
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: 'integer')]
	private int $price;

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $description = null;

	#[ORM\Column(type: 'integer')]
	private int $stock;

	#[ORM\OneToMany(mappedBy: 'plante', targetEntity: OrderItem::class, orphanRemoval: true)]
	private Collection $articles;

	public function __construct()
	{
		$this->articles = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getPrice(): int
	{
		return $this->price;
	}

	public function setPrice(int $price): self
	{
		$this->price = $price;
		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;
		return $this;
	}

	public function getStock(): int
	{
		return $this->stock;
	}

	public function setStock(int $stock): self
	{
		$this->stock = $stock;
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
			$item->setPlante($this);
		}
		return $this;
	}

	public function removeArticle(OrderItem $item): self
	{
		if ($this->articles->removeElement($item)) {
			if ($item->getPlante() === $this) {
				$item->setPlante(null);
			}
		}
		return $this;
	}
}
