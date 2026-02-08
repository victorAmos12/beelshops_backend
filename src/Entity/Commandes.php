<?php

namespace App\Entity;

use App\Repository\CommandesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandesRepository::class)]
class Commandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 100)]
    private ?string $numOrdre = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $prixTotal = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $adresseAppartement = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, ArticleCommande>
     */
    #[ORM\OneToMany(targetEntity: ArticleCommande::class, mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private Collection $articleCommandes;

    public function __construct()
    {
        $this->articleCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getNumOrdre(): ?string
    {
        return $this->numOrdre;
    }

    public function setNumOrdre(string $numOrdre): static
    {
        $this->numOrdre = $numOrdre;

        return $this;
    }

    public function getPrixTotal(): ?string
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(string $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?string $adresseLivraison): static
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getAdresseAppartement(): ?string
    {
        return $this->adresseAppartement;
    }

    public function setAdresseAppartement(?string $adresseAppartement): static
    {
        $this->adresseAppartement = $adresseAppartement;

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
     * @return Collection<int, ArticleCommande>
     */
    public function getArticleCommandes(): Collection
    {
        return $this->articleCommandes;
    }

    public function addArticleCommande(ArticleCommande $articleCommande): static
    {
        if (!$this->articleCommandes->contains($articleCommande)) {
            $this->articleCommandes->add($articleCommande);
            $articleCommande->setCommande($this);
        }

        return $this;
    }

    public function removeArticleCommande(ArticleCommande $articleCommande): static
    {
        if ($this->articleCommandes->removeElement($articleCommande)) {
            // set the owning side to null (unless already changed)
            if ($articleCommande->getCommande() === $this) {
                $articleCommande->setCommande(null);
            }
        }

        return $this;
    }
}
