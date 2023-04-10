<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Appointment'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['appointment:read']],
  order: ['id' => 'DESC'],
)]
#[ApiResource(
  uriTemplate: '/agents/{id}/appointments',
  types: ['https://schema.org/Appointment'],
  operations: [ new GetCollection() ],
  uriVariables: [ 'id' => new Link(fromProperty: 'appointments', fromClass: Agent::class) ],
  normalizationContext: ['groups' => ['appointment:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['fullName' => 'ipartial'])]
class Appointment
{
  use CreatedAtTrait, IsDeletedTrait;

  public ?bool $isConsultation = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['appointment:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['appointment:read'])]
    private ?bool $isComplete = false;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'Le médecin ou docteur doit être renseigné.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['appointment:read'])]
    private ?Agent $doctor = null;

    #[ORM\OneToOne(inversedBy: 'appointment', cascade: ['persist', 'remove'])]
    #[Groups(['appointment:read'])]
    private ?Consultation $consultation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['appointment:read', 'consult:read'])]
    private ?\DateTimeInterface $appointmentDate = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le patient doit être renseigné.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['appointment:read'])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[Groups(['appointment:read'])]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['appointment:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['appointment:read'])]
    private ?string $reason = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['appointment:read'])]
    private ?string $fullName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    public function getDoctor(): ?Agent
    {
        return $this->doctor;
    }

    public function setDoctor(?Agent $doctor): self
    {
        $this->doctor = $doctor;

        return $this;
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

    public function getAppointmentDate(): ?\DateTimeInterface
    {
        return $this->appointmentDate;
    }

    public function setAppointmentDate(?\DateTimeInterface $appointmentDate): self
    {
        $this->appointmentDate = $appointmentDate;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}
