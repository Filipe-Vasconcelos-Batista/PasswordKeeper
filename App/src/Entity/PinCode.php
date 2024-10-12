<?php

namespace App\Entity;

use App\Repository\PinCodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PinCodeRepository::class)]
class PinCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'pinCode', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $hashedPincode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getHashedPincode(): ?string
    {
        return $this->hashedPincode;
    }

    public function setHashedPincode(string $hashedPincode): static
    {
        $this->hashedPincode = $hashedPincode;

        return $this;
    }
}
