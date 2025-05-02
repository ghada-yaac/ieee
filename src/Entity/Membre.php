<?php

namespace App\Entity;

use App\Repository\MembreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MembreRepository::class)]
class Membre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $memberID = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mdp = null;

    #[ORM\Column]
    private ?int $numTel = null;

    #[ORM\Column(length: 255)]
    private ?string $membershipStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $membershipStartDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $membershipEndDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $role = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMemberID(): ?int
    {
        return $this->memberID;
    }

    public function setMemberID(int $memberID): static
    {
        $this->memberID = $memberID;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): static
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(int $numTel): static
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getMembershipStatus(): ?string
    {
        return $this->membershipStatus;
    }

    public function setMembershipStatus(string $membershipStatus): static
    {
        $this->membershipStatus = $membershipStatus;

        return $this;
    }

    public function getMembershipStartDate(): ?\DateTimeInterface
    {
        return $this->membershipStartDate;
    }

    public function setMembershipStartDate(\DateTimeInterface $membershipStartDate): static
    {
        $this->membershipStartDate = $membershipStartDate;

        return $this;
    }

    public function getMembershipEndDate(): ?\DateTimeInterface
    {
        return $this->membershipEndDate;
    }

    public function setMembershipEndDate(\DateTimeInterface $membershipEndDate): static
    {
        $this->membershipEndDate = $membershipEndDate;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }
}
