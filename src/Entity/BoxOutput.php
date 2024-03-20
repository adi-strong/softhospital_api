<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\BoxOutputRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BoxOutputRepository::class)]
#[ApiResource(
  types: ['https://schema.org/BoxOutput'],
  operations: [ new GetCollection(), new Post() ],
  normalizationContext: ['groups' => ['output:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['reason' => 'ipartial'])]
class BoxOutput
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['output:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boxOutputs')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'boxOutputs')]
    #[Groups(['output:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Groups(['output:read'])]
    private ?string $reason = null;

    #[ORM\Column(length: 255)]
    #[Groups(['output:read'])]
    private ?string $recipient = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['output:read'])]
    private ?string $amount = '0';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['output:read'])]
    private ?string $docRef = null;

    #[ORM\ManyToOne(inversedBy: 'boxOutputs')]
    #[Groups(['output:read'])]
    private ?ExpenseCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'boxOutputs')]
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

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;

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

    public function getCategory(): ?ExpenseCategory
    {
        return $this->category;
    }

    public function setCategory(?ExpenseCategory $category): self
    {
        $this->category = $category;

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
