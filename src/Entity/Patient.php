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
use App\AppTraits\PrivateKeyTrait;
use App\Repository\PatientRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PatientRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Patient'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['patient:read']],
  order: ['id' => 'DESC'],
)]
#[ApiResource(
  uriTemplate: '/covenant/{id}/patients',
  types: ['https://schema.org/Patient'],
  operations: [ new GetCollection() ],
  uriVariables: [ 'id' => new Link(fromProperty: 'patients', fromClass: Covenant::class) ],
  normalizationContext: ['groups' => ['patient:read']]
)]
#[ApiFilter(SearchFilter::class, properties: ['fullName' => 'ipartial'])]
class Patient
{
  use CreatedAtTrait, IsDeletedTrait, PrivateKeyTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom du patient doit être renseigné.')]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $sex = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['patient:read', 'covenant:read'])]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read', 'covenant:read'])]
    private ?string $birthPlace = null;

    #[ORM\Column(length: 12, nullable: true)]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $maritalStatus = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(
      min: 9,
      max: 20,
      minMessage: 'Ce champs doit contenir au moins {{ limit }} caractères.',
      maxMessage: 'Ce champs ne peut dépasser {{ limit }} caractères.')]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read', 'covenant:read', 'consult:read'])]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read'])]
    private ?string $father = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read', 'covenant:read'])]
    private ?string $mother = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read', 'covenant:read', 'consult:read'])]
    private ?string $address = null;

    #[ORM\ManyToOne]
    #[Groups(['patient:read', 'covenant:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'patients')]
    #[Groups(['patient:read', 'consult:read', 'nursing:read', 'invoice:read'])]
    private ?Covenant $covenant = null;

    #[ORM\ManyToOne]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?ImageObject $profile = null;

    #[ORM\ManyToOne(inversedBy: 'patients')]
    #[Assert\NotBlank(message: "Informations non valides.")]
    private ?Hospital $hospital = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
      'patient:read',
      'covenant:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
    ])]
    private ?int $age = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['patient:read', 'covenant:read', 'consult:read'])]
    private ?string $nationality = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Consultation::class)]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Nursing::class)]
    private Collection $nursings;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Lab::class)]
    private Collection $labs;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Prescription::class)]
    private Collection $prescriptions;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fullName = null;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->nursings = new ArrayCollection();
        $this->labs = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->birthPlace;
    }

    public function setBirthPlace(?string $birthPlace): self
    {
        $this->birthPlace = $birthPlace;

        return $this;
    }

    public function getMaritalStatus(): ?string
    {
        return $this->maritalStatus;
    }

    public function setMaritalStatus(?string $maritalStatus): self
    {
        $this->maritalStatus = $maritalStatus;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFather(): ?string
    {
        return $this->father;
    }

    public function setFather(?string $father): self
    {
        $this->father = $father;

        return $this;
    }

    public function getMother(): ?string
    {
        return $this->mother;
    }

    public function setMother(?string $mother): self
    {
        $this->mother = $mother;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    public function getCovenant(): ?Covenant
    {
        return $this->covenant;
    }

    public function setCovenant(?Covenant $covenant): self
    {
        $this->covenant = $covenant;

        return $this;
    }

    public function getProfile(): ?ImageObject
    {
        return $this->profile;
    }

    public function setProfile(?ImageObject $profile): self
    {
        $this->profile = $profile;

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

  #[Groups([
    'patient:read',
    'covenant:read',
    'consult:read',
    'lab:read',
    'prescript:read',
    'nursing:read',
    'appointment:read',
    'invoice:read',
  ])]
  public function getSlug(): ?string
  {
    return (new Slugify())->slugify($this->name.' '.($this->firstName ?? ''));
  }

  public function getAge(): ?int
  {
      return $this->age;
  }

  public function setAge(?int $age): self
  {
      $this->age = $age;

      return $this;
  }

  public function getNationality(): ?string
  {
      return $this->nationality;
  }

  public function setNationality(?string $nationality): self
  {
      $this->nationality = $nationality;

      return $this;
  }

  /**
   * @return Collection<int, Consultation>
   */
  public function getConsultations(): Collection
  {
      return $this->consultations;
  }

  public function addConsultation(Consultation $consultation): self
  {
      if (!$this->consultations->contains($consultation)) {
          $this->consultations->add($consultation);
          $consultation->setPatient($this);
      }

      return $this;
  }

  public function removeConsultation(Consultation $consultation): self
  {
      if ($this->consultations->removeElement($consultation)) {
          // set the owning side to null (unless already changed)
          if ($consultation->getPatient() === $this) {
              $consultation->setPatient(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Invoice>
   */
  public function getInvoices(): Collection
  {
      return $this->invoices;
  }

  public function addInvoice(Invoice $invoice): self
  {
      if (!$this->invoices->contains($invoice)) {
          $this->invoices->add($invoice);
          $invoice->setPatient($this);
      }

      return $this;
  }

  public function removeInvoice(Invoice $invoice): self
  {
      if ($this->invoices->removeElement($invoice)) {
          // set the owning side to null (unless already changed)
          if ($invoice->getPatient() === $this) {
              $invoice->setPatient(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Appointment>
   */
  public function getAppointments(): Collection
  {
      return $this->appointments;
  }

  public function addAppointment(Appointment $appointment): self
  {
      if (!$this->appointments->contains($appointment)) {
          $this->appointments->add($appointment);
          $appointment->setPatient($this);
      }

      return $this;
  }

  public function removeAppointment(Appointment $appointment): self
  {
      if ($this->appointments->removeElement($appointment)) {
          // set the owning side to null (unless already changed)
          if ($appointment->getPatient() === $this) {
              $appointment->setPatient(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Nursing>
   */
  public function getNursings(): Collection
  {
      return $this->nursings;
  }

  public function addNursing(Nursing $nursing): self
  {
      if (!$this->nursings->contains($nursing)) {
          $this->nursings->add($nursing);
          $nursing->setPatient($this);
      }

      return $this;
  }

  public function removeNursing(Nursing $nursing): self
  {
      if ($this->nursings->removeElement($nursing)) {
          // set the owning side to null (unless already changed)
          if ($nursing->getPatient() === $this) {
              $nursing->setPatient(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Lab>
   */
  public function getLabs(): Collection
  {
      return $this->labs;
  }

  public function addLab(Lab $lab): self
  {
      if (!$this->labs->contains($lab)) {
          $this->labs->add($lab);
          $lab->setPatient($this);
      }

      return $this;
  }

  public function removeLab(Lab $lab): self
  {
      if ($this->labs->removeElement($lab)) {
          // set the owning side to null (unless already changed)
          if ($lab->getPatient() === $this) {
              $lab->setPatient(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Prescription>
   */
  public function getPrescriptions(): Collection
  {
      return $this->prescriptions;
  }

  public function addPrescription(Prescription $prescription): self
  {
      if (!$this->prescriptions->contains($prescription)) {
          $this->prescriptions->add($prescription);
          $prescription->setPatient($this);
      }

      return $this;
  }

  public function removePrescription(Prescription $prescription): self
  {
      if ($this->prescriptions->removeElement($prescription)) {
          // set the owning side to null (unless already changed)
          if ($prescription->getPatient() === $this) {
              $prescription->setPatient(null);
          }
      }

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
