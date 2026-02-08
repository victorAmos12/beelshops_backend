<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, PanierArticles>
     */
    #[ORM\OneToMany(targetEntity: PanierArticles::class, mappedBy: 'panier_id', cascade: ['persist', 'remove'])]
    private Collection $panierArticles;

    public function __construct()
    {
        $this->panierArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

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

    /**
     * @return Collection<int, PanierArticles>
     */
    public function getPanierArticles(): Collection
    {
        return $this->panierArticles;
    }

    public function addPanierArticle(PanierArticles $panierArticle): static
    {
        if (!$this->panierArticles->contains($panierArticle)) {
            $this->panierArticles->add($panierArticle);
            $panierArticle->setPanierId($this);
        }

        return $this;
    }

    public function removePanierArticle(PanierArticles $panierArticle): static
    {
        if ($this->panierArticles->removeElement($panierArticle)) {
            // set the owning side to null (unless already changed)
            if ($panierArticle->getPanierId() === $this) {
                $panierArticle->setPanierId(null);
            }
        }

        return $this;
    }
}
