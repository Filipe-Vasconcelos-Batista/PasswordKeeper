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
    #[ORM\JoinColumn(nullable: false)]
    private ?User $UserId = null;

    #[ORM\Column(length: 4)]
    private ?string $pincode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->UserId;
    }

    public function setUserId(User $UserId): static
    {
        $this->UserId = $UserId;

        return $this;
    }

    public function getPincode(): ?string
    {
        return $this->pincode;
    }

    public function setPincode(string $pincode): static
    {
        $this->pincode = $pincode;

        return $this;
    }
}
