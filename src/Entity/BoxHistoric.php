<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
use App\Repository\BoxHistoricRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: BoxHistoricRepository::class)]
#[ApiResource]
class BoxHistoric
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['box:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boxHistorics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Box $box = null;

    #[ORM\Column(length: 8)]
    #[NotBlank(message: 'Le tag doit être renseigné.')]
    #[Choice(['input', 'output'], message: 'L\'information fournie est incorrecte.')]
    #[Groups(['box:read'])]
    private ?string $tag = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['box:read'])]
    #[NotBlank(message: 'Le montant doit être renseigné.')]
    private ?string $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBox(): ?Box
    {
        return $this->box;
    }

    public function setBox(?Box $box): self
    {
        $this->box = $box;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
