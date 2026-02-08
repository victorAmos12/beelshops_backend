<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $stock = null;

    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, PanierArticles>
     */
    #[ORM\OneToMany(targetEntity: PanierArticles::class, mappedBy: 'produit_id')]
    private Collection $panierArticles;

    /**
     * @var Collection<int, AvisClient>
     */
    #[ORM\OneToMany(targetEntity: AvisClient::class, mappedBy: 'id_produit')]
    private Collection $avisClients;

    /**
     * @var Collection<int, ListeSouhaits>
     */
    #[ORM\OneToMany(targetEntity: ListeSouhaits::class, mappedBy: 'id_produit')]
    private Collection $listeSouhaits;

    /**
     * @var Collection<int, ProduitImages>
     */
    #[ORM\OneToMany(targetEntity: ProduitImages::class, mappedBy: 'produit')]
    private Collection $produitImages;

    public function __construct()
    {
        $this->panierArticles = new ArrayCollection();
        $this->avisClients = new ArrayCollection();
        $this->listeSouhaits = new ArrayCollection();
        $this->produitImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCategory(): ?Categorie
    {
        return $this->category;
    }

    public function setCategory(?Categorie $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
            $panierArticle->setProduitId($this);
        }

        return $this;
    }

    public function removePanierArticle(PanierArticles $panierArticle): static
    {
        if ($this->panierArticles->removeElement($panierArticle)) {
            // set the owning side to null (unless already changed)
            if ($panierArticle->getProduitId() === $this) {
                $panierArticle->setProduitId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AvisClient>
     */
    public function getAvisClients(): Collection
    {
        return $this->avisClients;
    }

    public function addAvisClient(AvisClient $avisClient): static
    {
        if (!$this->avisClients->contains($avisClient)) {
            $this->avisClients->add($avisClient);
            $avisClient->setIdProduit($this);
        }

        return $this;
    }

    public function removeAvisClient(AvisClient $avisClient): static
    {
        if ($this->avisClients->removeElement($avisClient)) {
            // set the owning side to null (unless already changed)
            if ($avisClient->getIdProduit() === $this) {
                $avisClient->setIdProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ListeSouhaits>
     */
    public function getListeSouhaits(): Collection
    {
        return $this->listeSouhaits;
    }

    public function addListeSouhait(ListeSouhaits $listeSouhait): static
    {
        if (!$this->listeSouhaits->contains($listeSouhait)) {
            $this->listeSouhaits->add($listeSouhait);
            $listeSouhait->setIdProduit($this);
        }

        return $this;
    }

    public function removeListeSouhait(ListeSouhaits $listeSouhait): static
    {
        if ($this->listeSouhaits->removeElement($listeSouhait)) {
            // set the owning side to null (unless already changed)
            if ($listeSouhait->getIdProduit() === $this) {
                $listeSouhait->setIdProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ProduitImages>
     */
    public function getProduitImages(): Collection
    {
        return $this->produitImages;
    }

    public function addProduitImage(ProduitImages $produitImage): static
    {
        if (!$this->produitImages->contains($produitImage)) {
            $this->produitImages->add($produitImage);
            $produitImage->setProduit($this);
        }

        return $this;
    }

    public function removeProduitImage(ProduitImages $produitImage): static
    {
        if ($this->produitImages->removeElement($produitImage)) {
            // set the owning side to null (unless already changed)
            if ($produitImage->getProduit() === $this) {
                $produitImage->setProduit(null);
            }
        }

        return $this;
    }
}
