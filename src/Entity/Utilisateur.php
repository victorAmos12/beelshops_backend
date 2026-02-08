<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_CLIENT'];

    /**
     * @var Collection<int, AvisClient>
     */
    #[ORM\OneToMany(targetEntity: AvisClient::class, mappedBy: 'id_utilisateur')]
    private Collection $avisClients;

    /**
     * @var Collection<int, ListeSouhaits>
     */
    #[ORM\OneToMany(targetEntity: ListeSouhaits::class, mappedBy: 'id_utilisateur')]
    private Collection $listeSouhaits;

    /**
     * @var Collection<int, Commandes>
     */
    #[ORM\OneToMany(targetEntity: Commandes::class, mappedBy: 'utilisateur')]
    private Collection $commandes;

    public function __construct()
    {
        $this->avisClients = new ArrayCollection();
        $this->listeSouhaits = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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
            $avisClient->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeAvisClient(AvisClient $avisClient): static
    {
        if ($this->avisClients->removeElement($avisClient)) {
            // set the owning side to null (unless already changed)
            if ($avisClient->getIdUtilisateur() === $this) {
                $avisClient->setIdUtilisateur(null);
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
            $listeSouhait->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeListeSouhait(ListeSouhaits $listeSouhait): static
    {
        if ($this->listeSouhaits->removeElement($listeSouhait)) {
            // set the owning side to null (unless already changed)
            if ($listeSouhait->getIdUtilisateur() === $this) {
                $listeSouhait->setIdUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commandes>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commandes $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commandes $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): static
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): static
    {
        $this->roles = array_filter($this->roles, fn($r) => $r !== $role);

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * Implémentation de UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email ?? '';
    }

    /**
     * Implémentation de UserInterface
     */
    public function eraseCredentials(): void
    {
        // Si vous stockez des données sensibles temporaires sur l'utilisateur, effacez-les ici
    }

    /**
     * Implémentation de PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
