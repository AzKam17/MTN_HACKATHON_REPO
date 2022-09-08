<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Carbon\CarbonImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 't_transactions')]
#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
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
}
