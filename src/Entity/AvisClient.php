<?php

namespace App\Entity;

use App\Repository\AvisClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisClientRepository::class)]
class AvisClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avisClients')]
    private ?Produit $id_produit = null;

    #[ORM\ManyToOne(inversedBy: 'avisClients')]
    private ?Utilisateur $id_utilisateur = null;

    #[ORM\Column(type: 'integer')]
    private ?int $rating = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->id_produit;
    }

    public function setIdProduit(?Produit $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $id_utilisateur): static
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
