<?php

namespace App\Entity;

use App\Repository\UserTontineRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 't_user_tontines')]
#[ORM\Entity(repositoryClass: UserTontineRepository::class)]
class UserTontine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Utilisateur
    #[ORM\ManyToOne(inversedBy: 'userTontines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // Tontine
    #[ORM\ManyToOne(inversedBy: 'membres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tontine $tontine = null;

    // Date d'ajout dans la tontine
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Si true alors l'utilisateur a ete supprime de la tontine
    #[ORM\Column]
    private ?bool $isRemoved = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTontine(): ?Tontine
    {
        return $this->tontine;
    }

    public function setTontine(?Tontine $tontine): self
    {
        $this->tontine = $tontine;

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

    public function isIsRemoved(): ?bool
    {
        return $this->isRemoved;
    }

    public function setIsRemoved(bool $isRemoved): self
    {
        $this->isRemoved = $isRemoved;

        return $this;
    }


    #[ORM\PrePersist]
    public function prePersistOps()
    {
        $this->setIsRemoved(false);
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'user' => $this->getUser()->toArray(),
            'createdAt' => $this->getCreatedAt(),
        ];
    }
}
