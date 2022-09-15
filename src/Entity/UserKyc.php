<?php

namespace App\Entity;

use App\Repository\UserKycRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserKycRepository::class)]
class UserKyc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idcard = null;

    #[ORM\Column(length: 255)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $verifString = null;

    #[ORM\OneToOne(inversedBy: 'userKyc', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdcard(): ?string
    {
        return $this->idcard;
    }

    public function setIdcard(string $idcard): self
    {
        $this->idcard = $idcard;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getVerifString(): ?string
    {
        return $this->verifString;
    }

    public function setVerifString(string $verifString): self
    {
        $this->verifString = $verifString;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
