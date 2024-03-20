<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\BoxInputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BoxInputRepository::class)]
#[ApiResource(
  types: ['https://schema.org/BoxInput'],
  operations: [ new GetCollection(), new Post() ],
  normalizationContext: ['groups' => ['input:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['reason' => 'ipartial'])]
class BoxInput
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['input:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boxInputs')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'boxInputs')]
    #[Groups(['input:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['input:read'])]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    #[Groups(['input:read'])]
    private ?string $porter = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['input:read'])]
    private ?string $amount = '0';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['input:read'])]
    private ?string $docRef = null;

    #[ORM\ManyToOne(inversedBy: 'boxInputs')]
    private ?Box $box = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

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

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getPorter(): ?string
    {
        return $this->porter;
    }

    public function setPorter(string $porter): self
    {
        $this->porter = $porter;

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

    public function getDocRef(): ?string
    {
        return $this->docRef;
    }

    public function setDocRef(?string $docRef): self
    {
        $this->docRef = $docRef;

        return $this;
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
}
