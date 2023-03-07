<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\AgentRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AgentRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Agent'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch()
  ],
  normalizationContext: ['groups' => ['agent:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: [
  'name' => 'ipartial',
  'lastName' => 'ipartial',
  'firstName' => 'ipartial',
])]
class Agent
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['agent:read', 'user:read', 'medicine:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de l\'agent doit être renseigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.',
      maxMessage: 'Ce champs doit faire {{ limit }} caractères au maximum.',
    )]
    #[Groups(['agent:read', 'user:read', 'medicine:read', 'consult:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['agent:read', 'user:read', 'medicine:read', 'consult:read'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['agent:read', 'user:read', 'medicine:read', 'consult:read'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 4, nullable: true)]
    #[Assert\Choice(['H', 'F', 'none'])]
    #[Groups(['agent:read', 'user:read'])]
    private ?string $sex = null;

    #[ORM\Column(length: 16)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone doit être renseigné.')]
    #[Assert\Length(min: 9, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    #[Assert\Regex('#^([+]\d{2}[-. ])?\d{9,14}$#', message: 'Numéro de téléphone non valide.')]
    #[Groups(['agent:read', 'user:read'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Adresse email invalide.')]
    #[Groups(['agent:read', 'user:read'])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[Assert\NotBlank(message: 'La fonction doit être renseigné.')]
    #[Groups(['agent:read', 'user:read', 'consult:read'])]
    private ?Office $office = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[Groups(['agent:read', 'user:read'])]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'agents')]
    #[Groups(['agent:read'])]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'agent', cascade: ['persist', 'remove'])]
    #[Groups(['agent:read'])]
    private ?User $userAccount = null;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Consultation::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', unique: false)]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'doctor', targetEntity: Appointment::class)]
    private Collection $appointments;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->appointments = new ArrayCollection();
    }

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

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

    public function getOffice(): ?Office
    {
        return $this->office;
    }

    public function setOffice(?Office $office): self
    {
        $this->office = $office;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

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

  #[Groups(['agent:read'])]
  public function getSlug(): string
  {
    return (new Slugify())->slugify($this->name.' '.$this->firstName);
  }

  public function getUserAccount(): ?User
  {
      return $this->userAccount;
  }

  public function setUserAccount(?User $userAccount): self
  {
      $this->userAccount = $userAccount;

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
          $consultation->setDoctor($this);
      }

      return $this;
  }

  public function removeConsultation(Consultation $consultation): self
  {
      if ($this->consultations->removeElement($consultation)) {
          // set the owning side to null (unless already changed)
          if ($consultation->getDoctor() === $this) {
              $consultation->setDoctor(null);
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
          $appointment->setDoctor($this);
      }

      return $this;
  }

  public function removeAppointment(Appointment $appointment): self
  {
      if ($this->appointments->removeElement($appointment)) {
          // set the owning side to null (unless already changed)
          if ($appointment->getDoctor() === $this) {
              $appointment->setDoctor(null);
          }
      }

      return $this;
  }
}
