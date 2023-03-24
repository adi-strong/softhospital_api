<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\AppTraits\FullNameTrait;
use App\Repository\PrescriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PrescriptionRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Prescription'],
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['prescript:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(SearchFilter::class, properties: ['fullName' => 'ipartial'])]
class Prescription
{
  use FullNameTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['prescript:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'prescription')]
    #[Groups(['prescript:read'])]
    private ?Consultation $consultation = null;

    #[ORM\OneToOne(inversedBy: 'prescription')]
    #[Groups(['prescript:read'])]
    private ?Lab $lab = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[Groups(['prescript:read'])]
    private ?Patient $patient = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['prescript:read'])]
    private ?string $descriptions = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    #[Groups(['prescript:read'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['prescript:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['prescript:read'])]
    private ?bool $isPublished = null;

    #[ORM\ManyToOne(inversedBy: 'prescriptions')]
    private ?Hospital $hospital = null;

    #[ORM\Column(nullable: true)]
    private ?array $orders = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    public function getLab(): ?Lab
    {
        return $this->lab;
    }

    public function setLab(?Lab $lab): self
    {
        $this->lab = $lab;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(?string $descriptions): self
    {
        $this->descriptions = $descriptions;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
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

  #[Groups(['prescript:read'])]
  public function getPrescriptionNumber(): ?string
  {
    return sprintf('%05d', $this->id);
  }

  public function getOrders(): ?array
  {
      return $this->orders;
  }

  public function setOrders(?array $orders): self
  {
    $this->orders = $orders;

    return $this;
  }
}
