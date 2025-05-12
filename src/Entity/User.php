<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: 'string', length: 255, unique: true)]
	private ?string $email = null;

	#[ORM\Column(type: 'string')]
	private ?string $password = null;

	#[ORM\Column(type: 'boolean')]
	private bool $admin = false;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?\DateTimeInterface $emailVerifiedAt = null;

	#[ORM\Column(type: 'string', length: 100, nullable: true)]
	private ?string $rememberToken = null;

	#[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Order::class, orphanRemoval: true)]
	private Collection $commandes;

	public function __construct()
	{
		$this->commandes = new ArrayCollection();
	}

	// Getters & setters

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

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): self
	{
		$this->email = $email;
		return $this;
	}

	public function getPassword(): ?string
	{
		return $this->password;
	}

	public function setPassword(string $password): self
	{
		$this->password = $password;
		return $this;
	}

	public function isAdmin(): bool
	{
		return $this->admin;
	}

	public function setAdmin(bool $admin): self
	{
		$this->admin = $admin;
		return $this;
	}

	public function getEmailVerifiedAt(): ?\DateTimeInterface
	{
		return $this->emailVerifiedAt;
	}

	public function setEmailVerifiedAt(?\DateTimeInterface $date): self
	{
		$this->emailVerifiedAt = $date;
		return $this;
	}

	public function getRememberToken(): ?string
	{
		return $this->rememberToken;
	}

	public function setRememberToken(?string $token): self
	{
		$this->rememberToken = $token;
		return $this;
	}

	/**
	 * @return Collection<int, Order>
	 */
	public function getCommandes(): Collection
	{
		return $this->commandes;
	}

	public function addCommande(Order $commande): self
	{
		if (!$this->commandes->contains($commande)) {
			$this->commandes[] = $commande;
			$commande->setUtilisateur($this);
		}
		return $this;
	}

	public function removeCommande(Order $commande): self
	{
		if ($this->commandes->removeElement($commande)) {
			if ($commande->getUtilisateur() === $this) {
				$commande->setUtilisateur(null);
			}
		}
		return $this;
	}
}
