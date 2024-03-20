<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
use App\Repository\DestockingOfMedicinesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DestockingOfMedicinesRepository::class)]
#[ApiResource(
  types: ['https://schema.org/DestockingOfMedicines'],
  operations: [ ],
  normalizationContext: ['groups' => ['destocking:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
class DestockingOfMedicines
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'destockingOfMedicines')]
    private ?Medicine $medicine = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?string $loss = '0';

    #[ORM\ManyToOne(inversedBy: 'destockingOfMedicines')]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?float $quantity = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?string $cost = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicine:read', 'destocking:read'])]
    private ?string $price = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMedicine(): ?Medicine
    {
        return $this->medicine;
    }

    public function setMedicine(?Medicine $medicine): self
    {
        $this->medicine = $medicine;

        return $this;
    }

    public function getLoss(): ?string
    {
        return $this->loss;
    }

    public function setLoss(?string $loss): self
    {
        $this->loss = $loss;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }
}
