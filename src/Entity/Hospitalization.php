<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\HospitalizationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalizationRepository::class)]
#[ApiResource]
class Hospitalization
{
  public ?bool $isBedroomLeaved = false;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['consult:read', 'invoice:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'hospitalization')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'La consultation doit être renseignée.')]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'hospitalizations')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'Le lit doit être renseigné.')]
    #[Groups(['consult:read', 'invoice:read'])]
    private ?Bed $bed = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]

    #[Assert\NotBlank(message: 'Le prix doit être renseigné.')]
    #[Groups(['consult:read', 'invoice:read'])]
    private ?string $price = '0';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['invoice:read'])]
    private ?\DateTimeInterface $releasedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?\DateTimeInterface $leaveAt = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le nombre de jours effétué est inconnu.')]
    #[Groups(['invoice:read'])]
    private ?int $daysCounter = 1;

    #[ORM\ManyToOne(inversedBy: 'hospitalizations')]
    private ?Hospital $hospital = null;

    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?bool $isCompleted = false;

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

    public function getBed(): ?Bed
    {
        return $this->bed;
    }

    public function setBed(?Bed $bed): self
    {
        $this->bed = $bed;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getLeaveAt(): ?\DateTimeInterface
    {
        return $this->leaveAt;
    }

    public function setLeaveAt(?\DateTimeInterface $leaveAt): self
    {
        $this->leaveAt = $leaveAt;

        return $this;
    }

    public function getDaysCounter(): ?int
    {
        return $this->daysCounter;
    }

    public function setDaysCounter(int $daysCounter): self
    {
        $this->daysCounter = $daysCounter;

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

    public function isIsCompleted(): ?bool
    {
        return $this->isCompleted;
    }

    public function setIsCompleted(bool $isCompleted): self
    {
        $this->isCompleted = $isCompleted;

        return $this;
    }
}
