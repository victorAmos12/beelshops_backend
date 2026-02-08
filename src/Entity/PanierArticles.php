<?php

namespace App\Entity;

use App\Repository\PanierArticlesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierArticlesRepository::class)]
class PanierArticles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'panierArticles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier_id = null;

    #[ORM\ManyToOne(inversedBy: 'panierArticles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit_id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $addedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPanierId(): ?Panier
    {
        return $this->panier_id;
    }

    public function setPanierId(?Panier $panier_id): static
    {
        $this->panier_id = $panier_id;

        return $this;
    }

    public function getProduitId(): ?Produit
    {
        return $this->produit_id;
    }

    public function setProduitId(?Produit $produit_id): static
    {
        $this->produit_id = $produit_id;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): static
    {
        $this->addedAt = $addedAt;

        return $this;
    }
}
