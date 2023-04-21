<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\FullNameTrait;
use App\Repository\LabRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LabRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Lab'],
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['lab:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['fullName' => 'ipartial'])]
class Lab
{
  use CreatedAtTrait, FullNameTrait;

  public ?array $values = [];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'labs')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'labs')]
    #[Groups(['lab:read'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'lab', targetEntity: LabResult::class, cascade: ['persist', 'remove'])]
    #[Groups(['lab:read', 'prescript:read'])]
    private Collection $labResults;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?string $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['lab:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(inversedBy: 'lab')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['lab:read'])]
    private ?Consultation $consultation = null;

    #[ORM\ManyToOne(inversedBy: 'labs')]
    #[Groups(['lab:read'])]
    private ?Patient $patient = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['lab:read'])]
    private ?bool $isPublished = false;

    #[ORM\OneToOne(mappedBy: 'lab', cascade: ['persist', 'remove'])]
    private ?Prescription $prescription = null;

    #[ORM\ManyToOne(inversedBy: 'labPrescribers')]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?User $userPrescriber = null;

    #[ORM\ManyToOne(inversedBy: 'labPublishers')]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?User $userPublisher = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['lab:read', 'prescript:read'])]
    private ?array $results = [];

    public function __construct()
    {
        $this->labResults = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LabResult>
     */
    public function getLabResults(): Collection
    {
        return $this->labResults;
    }

    public function addLabResult(LabResult $labResult): self
    {
        if (!$this->labResults->contains($labResult)) {
            $this->labResults->add($labResult);
            $labResult->setLab($this);
        }

        return $this;
    }

    public function removeLabResult(LabResult $labResult): self
    {
        if ($this->labResults->removeElement($labResult)) {
            // set the owning side to null (unless already changed)
            if ($labResult->getLab() === $this) {
                $labResult->setLab(null);
            }
        }

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

  #[Groups(['lab:read'])]
  public function getLabNumber(): ?string
  {
    return sprintf('%05d', $this->id);
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

  public function isIsPublished(): ?bool
  {
      return $this->isPublished;
  }

  public function setIsPublished(?bool $isPublished): self
  {
      $this->isPublished = $isPublished;

      return $this;
  }

  public function getPrescription(): ?Prescription
  {
      return $this->prescription;
  }

  public function setPrescription(?Prescription $prescription): self
  {
      // unset the owning side of the relation if necessary
      if ($prescription === null && $this->prescription !== null) {
          $this->prescription->setLab(null);
      }

      // set the owning side of the relation if necessary
      if ($prescription !== null && $prescription->getLab() !== $this) {
          $prescription->setLab($this);
      }

      $this->prescription = $prescription;

      return $this;
  }

  public function getUserPrescriber(): ?User
  {
      return $this->userPrescriber;
  }

  public function setUserPrescriber(?UserInterface $userPrescriber): self
  {
      $this->userPrescriber = $userPrescriber;

      return $this;
  }

  public function getUserPublisher(): ?User
  {
      return $this->userPublisher;
  }

  public function setUserPublisher(?UserInterface $userPublisher): self
  {
      $this->userPublisher = $userPublisher;

      return $this;
  }

  public function getResults(): ?array
  {
      return $this->results;
  }

  public function setResults(?array $results): self
  {
      $this->results = $results;

      return $this;
  }
}
