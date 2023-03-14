<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\AppTraits\CreatedAtTrait;
use App\Repository\NursingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NursingRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Prescription'],
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['nursing:read']],
  order: ['id' => 'DESC'],
)]
class Nursing
{
  use CreatedAtTrait;

  public ?array $treatments = [];

  public ?bool $isNursingCompleted = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['nursing:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'nursing')]
    #[Groups(['nursing:read'])]
    private ?Consultation $consultation = null;

    #[ORM\OneToMany(mappedBy: 'nursing', targetEntity: NursingTreatment::class, cascade: ['remove', 'persist'])]
    #[Groups(['nursing:read'])]
    private Collection $nursingTreatments;

    #[ORM\ManyToOne(inversedBy: 'nursings')]
    #[Groups(['nursing:read'])]
    private ?Patient $patient = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?bool $isPublished = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'nursings')]
    private ?Hospital $hospital = null;

    #[ORM\Column]
    #[Groups(['nursing:read'])]
    private ?bool $isCompleted = false;

    public function __construct()
    {
        $this->nursingTreatments = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, NursingTreatment>
     */
    public function getNursingTreatments(): Collection
    {
        return $this->nursingTreatments;
    }

    public function addNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if (!$this->nursingTreatments->contains($nursingTreatment)) {
            $this->nursingTreatments->add($nursingTreatment);
            $nursingTreatment->setNursing($this);
        }

        return $this;
    }

    public function removeNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if ($this->nursingTreatments->removeElement($nursingTreatment)) {
            // set the owning side to null (unless already changed)
            if ($nursingTreatment->getNursing() === $this) {
                $nursingTreatment->setNursing(null);
            }
        }

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

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

  #[Groups(['nursing:read'])]
  public function getNursingNumber(): ?string
  {
    return sprintf('%05d', $this->id);
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
