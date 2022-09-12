<?php

namespace App\Entity;

use App\Repository\TontineRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 't_tontines')]
#[ORM\Entity(repositoryClass: TontineRepository::class)]
class Tontine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    //Solde de la tontine
    #[ORM\Column(length: 255)]
    private ?float $solde = null;

    //Montant de la cotisation
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?MontantTontine $montant = null;

    //Periodicite de la cotisation
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PeriodiciteTontine $periodicite = null;

    //Createur de la tontine
    #[ORM\ManyToOne(inversedBy: 'createdTontines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    //Code de la tontine
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    //Membres de la tontine
    #[ORM\OneToMany(mappedBy: 'tontine', targetEntity: UserTontine::class, cascade: ['persist', 'remove'])]
    private Collection $membres;

    //Nombre de tours
    #[ORM\Column]
    private ?int $compteur = null;

    #[ORM\OneToOne(mappedBy: 'tontine', cascade: ['persist', 'remove'])]
    private ?ListeRetrait $listeRetrait = null;

    #[ORM\OneToMany(mappedBy: 'tontine', targetEntity: Cotisation::class)]
    private Collection $cotisations;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->cotisations = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersistOps()
    {
        $this->setCompteur(0);
        $slugger = new AsciiSlugger();
        $this->setSlug(
            strtolower($slugger->slug(
                $this->getNom()
            )->toString())
        );
        $this->setIsActive(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getMontant(): ?MontantTontine
    {
        return $this->montant;
    }

    public function setMontant(?MontantTontine $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getPeriodicite(): ?PeriodiciteTontine
    {
        return $this->periodicite;
    }

    public function setPeriodicite(?PeriodiciteTontine $periodicite): self
    {
        $this->periodicite = $periodicite;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, UserTontine>
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(UserTontine $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres->add($membre);
            $membre->setTontine($this);
        }

        return $this;
    }

    public function removeMembre(UserTontine $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getTontine() === $this) {
                $membre->setTontine(null);
            }
        }

        return $this;
    }

    public function getCompteur(): ?int
    {
        return $this->compteur;
    }

    public function setCompteur(int $compteur): self
    {
        $this->compteur = $compteur;

        return $this;
    }

    public function getListeRetrait(): ?ListeRetrait
    {
        return $this->listeRetrait;
    }

    public function setListeRetrait(ListeRetrait $listeRetrait): self
    {
        // set the owning side of the relation if necessary
        if ($listeRetrait->getTontine() !== $this) {
            $listeRetrait->setTontine($this);
        }

        $this->listeRetrait = $listeRetrait;

        return $this;
    }

    /**
     * @return Collection<int, Cotisation>
     */
    public function getCotisations(): Collection
    {
        return $this->cotisations;
    }

    public function addCotisation(Cotisation $cotisation): self
    {
        if (!$this->cotisations->contains($cotisation)) {
            $this->cotisations->add($cotisation);
            $cotisation->setTontine($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): self
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getTontine() === $this) {
                $cotisation->setTontine(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): self
    {
        $this->createdAt = CarbonImmutable::now();

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function setUpdatedAt(): self
    {
        $this->updatedAt = Carbon::now();

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
