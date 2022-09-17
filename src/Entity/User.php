<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 't_users')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $tel = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Tontine::class)]
    private Collection $createdTontines;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserTontine::class)]
    private Collection $userTontines;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cotisation::class)]
    private Collection $cotisations;

    #[ORM\Column]
    private ?bool $kycVerified = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?UserKyc $userKyc = null;

    public function __construct()
    {
        $this->createdTontines = new ArrayCollection();
        $this->userTontines = new ArrayCollection();
        $this->cotisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    #[ORM\PrePersist]
    public function setSlug(): self
    {
        $slugger = new AsciiSlugger();
        $this->slug = $slugger->slug(
            $this->nom . ' ' .
            $this->prenom.' '.
            $this->tel
        )->lower();
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

    #[ORM\PrePersist]
    public function prePersistOps()
    {
        if ($this->solde === null) {
            $this->setSolde(0);
        }
        $this->setKycVerified(false);
    }

    /**
     * @return Collection<int, Tontine>
     */
    public function getCreatedTontines(): Collection
    {
        return $this->createdTontines;
    }

    public function addCreatedTontine(Tontine $createdTontine): self
    {
        if (!$this->createdTontines->contains($createdTontine)) {
            $this->createdTontines->add($createdTontine);
            $createdTontine->setCreatedBy($this);
        }

        return $this;
    }

    public function removeCreatedTontine(Tontine $createdTontine): self
    {
        if ($this->createdTontines->removeElement($createdTontine)) {
            // set the owning side to null (unless already changed)
            if ($createdTontine->getCreatedBy() === $this) {
                $createdTontine->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserTontine>
     */
    public function getUserTontines(): Collection
    {
        return $this->userTontines;
    }

    public function addUserTontine(UserTontine $userTontine): self
    {
        if (!$this->userTontines->contains($userTontine)) {
            $this->userTontines->add($userTontine);
            $userTontine->setUser($this);
        }

        return $this;
    }

    public function removeUserTontine(UserTontine $userTontine): self
    {
        if ($this->userTontines->removeElement($userTontine)) {
            // set the owning side to null (unless already changed)
            if ($userTontine->getUser() === $this) {
                $userTontine->setUser(null);
            }
        }

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
            $cotisation->setUser($this);
        }

        return $this;
    }

    public function removeCotisation(Cotisation $cotisation): self
    {
        if ($this->cotisations->removeElement($cotisation)) {
            // set the owning side to null (unless already changed)
            if ($cotisation->getUser() === $this) {
                $cotisation->setUser(null);
            }
        }

        return $this;
    }

    public function isKycVerified(): ?bool
    {
        return $this->kycVerified;
    }

    public function setKycVerified(bool $kycVerified): self
    {
        $this->kycVerified = $kycVerified;

        return $this;
    }

    public function getUserKyc(): ?UserKyc
    {
        return $this->userKyc;
    }

    public function setUserKyc(UserKyc $userKyc): self
    {
        // set the owning side of the relation if necessary
        if ($userKyc->getUser() !== $this) {
            $userKyc->setUser($this);
        }

        $this->userKyc = $userKyc;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'tel' => $this->getTel(),
            'solde' => $this->getSolde(),
            'slug' => $this->getSlug(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $this->getUpdatedAt()->format('Y-m-d H:i:s')
        ];
    }
}
