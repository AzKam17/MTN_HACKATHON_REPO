<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 't_transactions')]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    public const TYPE_DEPOT = 'depot';
    public const TYPE_COTISATION = 'tontine_cotisation';
    public const TYPE_RETRAIT = 'retrait';
    public const TYPE_TRANSFERT = 'transfert';
    public const TYPE_DEPOT_COTISATION = 'depot_cotisation';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idSdr = null;

    #[ORM\Column]
    private ?int $idRcv = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $typeRcv = null;

    #[ORM\Column(length: 255)]
    private ?string $typeSdr = null;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column]
    #[Assert\Range(
        notInRangeMessage: 'Le montant doit être compris entre {{ min }} et {{ max }}',
        min: 0,
        max: 1000000000,
    )]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $additionalData = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdSdr(): ?int
    {
        return $this->idSdr;
    }

    public function setIdSdr(int $idSdr): self
    {
        $this->idSdr = $idSdr;

        return $this;
    }

    public function getIdRcv(): ?int
    {
        return $this->idRcv;
    }

    public function setIdRcv(int $idRcv): self
    {
        $this->idRcv = $idRcv;

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

    public function getTypeRcv(): ?string
    {
        return $this->typeRcv;
    }

    public function setTypeRcv(string $typeRcv): self
    {
        $this->typeRcv = $typeRcv;

        return $this;
    }

    public function getTypeSdr(): ?string
    {
        return $this->typeSdr;
    }

    public function setTypeSdr(string $typeSdr): self
    {
        $this->typeSdr = $typeSdr;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function ifTontine(): bool
    {
        return $this->typeRcv === 'tontine' || $this->typeSdr === 'tontine';
    }

    public function whoIsTontine(): string
    {
        return $this->typeRcv === 'tontine' ? 'receiver' : 'sender';
    }

    public function getTontineId(): int
    {
        return $this->whoIsTontine() === 'receiver' ? $this->idRcv : $this->idSdr;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'idSdr' => $this->idSdr,
            'idRcv' => $this->idRcv,
            'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
            'typeRcv' => $this->typeRcv,
            'typeSdr' => $this->typeSdr,
            'state' => $this->state,
            'montant' => $this->montant,
            'type' => $this->type,
        ];
    }

    public function getAdditionalData(): ?string
    {
        return $this->additionalData;
    }

    public function setAdditionalData(?string $additionalData): self
    {
        $this->additionalData = $additionalData;

        return $this;
    }
}
