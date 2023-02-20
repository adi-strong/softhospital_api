<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\ConsultationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Consultation'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['consult:read']],
  order: ['id' => 'DESC'],
)]
class Consultation
{
  use CreatedAtTrait, IsDeletedTrait;
  
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['consult:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Le patient doit être renseigné.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['consult:read'])]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['consult:read'])]
    private ?ConsultationsType $file = null;

    #[ORM\ManyToMany(targetEntity: Act::class, inversedBy: 'consultations')]
    #[Groups(['consult:read'])]
    private Collection $acts;

    #[ORM\ManyToMany(targetEntity: Exam::class, inversedBy: 'consultations')]
    #[Groups(['consult:read'])]
    private Collection $exams;

    #[ORM\ManyToMany(targetEntity: Treatment::class, inversedBy: 'consultations')]
    #[Groups(['consult:read'])]
    private Collection $treatments;

    #[ORM\OneToOne(mappedBy: 'consultation', cascade: ['persist', 'remove'])]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Groups(['consult:read'])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Groups(['consult:read'])]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['consult:read'])]
    private ?bool $isComplete = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: '0', nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $temperature = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2, nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $weight = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $arterialTension = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $cardiacFrequency = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $respiratoryFrequency = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['consult:read'])]
    private ?string $oxygenSaturation = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'La fiche doit être renseignée.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['consult:read'])]
    private ?Agent $doctor = null;

    #[ORM\OneToOne(mappedBy: 'consultation', cascade: ['persist', 'remove'])]
    private ?Appointment $appointment = null;

    public function __construct()
    {
        $this->acts = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->treatments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFile(): ?ConsultationsType
    {
        return $this->file;
    }

    public function setFile(?ConsultationsType $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection<int, Act>
     */
    public function getActs(): Collection
    {
        return $this->acts;
    }

    public function addAct(Act $act): self
    {
        if (!$this->acts->contains($act)) {
            $this->acts->add($act);
        }

        return $this;
    }

    public function removeAct(Act $act): self
    {
        $this->acts->removeElement($act);

        return $this;
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams->add($exam);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        $this->exams->removeElement($exam);

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    public function addTreatment(Treatment $treatment): self
    {
        if (!$this->treatments->contains($treatment)) {
            $this->treatments->add($treatment);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): self
    {
        $this->treatments->removeElement($treatment);

        return $this;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        // set the owning side of the relation if necessary
        if ($invoice->getConsultation() !== $this) {
            $invoice->setConsultation($this);
        }

        $this->invoice = $invoice;

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

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getTemperature(): ?string
    {
        return $this->temperature;
    }

    public function setTemperature(?string $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getArterialTension(): ?string
    {
        return $this->arterialTension;
    }

    public function setArterialTension(?string $arterialTension): self
    {
        $this->arterialTension = $arterialTension;

        return $this;
    }

    public function getCardiacFrequency(): ?string
    {
        return $this->cardiacFrequency;
    }

    public function setCardiacFrequency(?string $cardiacFrequency): self
    {
        $this->cardiacFrequency = $cardiacFrequency;

        return $this;
    }

    public function getRespiratoryFrequency(): ?string
    {
        return $this->respiratoryFrequency;
    }

    public function setRespiratoryFrequency(?string $respiratoryFrequency): self
    {
        $this->respiratoryFrequency = $respiratoryFrequency;

        return $this;
    }

    public function getOxygenSaturation(): ?string
    {
        return $this->oxygenSaturation;
    }

    public function setOxygenSaturation(?string $oxygenSaturation): self
    {
        $this->oxygenSaturation = $oxygenSaturation;

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

    public function getAppointment(): ?Appointment
    {
        return $this->appointment;
    }

    public function setAppointment(?Appointment $appointment): self
    {
        // unset the owning side of the relation if necessary
        if ($appointment === null && $this->appointment !== null) {
            $this->appointment->setConsultation(null);
        }

        // set the owning side of the relation if necessary
        if ($appointment !== null && $appointment->getConsultation() !== $this) {
            $appointment->setConsultation($this);
        }

        $this->appointment = $appointment;

        return $this;
    }
}
