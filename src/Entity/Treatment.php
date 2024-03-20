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
use App\Repository\TreatmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TreatmentRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Treatment'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['treatment:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['wording' => 'ipartial'])]
class Treatment
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['treatment:read', 'consult:read', 'nursing:read', 'invoice:read', 'act:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libellé doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['treatment:read', 'consult:read', 'nursing:read', 'invoice:read', 'act:read'])]
    private ?string $wording = null;

    #[ORM\ManyToOne(inversedBy: 'treatments')]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['treatment:read', 'nursing:read', 'act:read'])]
    private ?string $price = '0';

    #[ORM\ManyToOne(inversedBy: 'treatments')]
    #[Groups(['treatment:read'])]
    private ?TreatmentCategory $category = null;

    #[ORM\ManyToMany(targetEntity: Consultation::class, mappedBy: 'treatments')]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'treatment', targetEntity: NursingTreatment::class, cascade: ['persist', 'remove'])]
    private Collection $nursingTreatments;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->nursingTreatments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?TreatmentCategory
    {
        return $this->category;
    }

    public function setCategory(?TreatmentCategory $category): self
    {
        $this->category = $category;

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
            $consultation->addTreatment($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            $consultation->removeTreatment($this);
        }

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
            $nursingTreatment->setTreatment($this);
        }

        return $this;
    }

    public function removeNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if ($this->nursingTreatments->removeElement($nursingTreatment)) {
            // set the owning side to null (unless already changed)
            if ($nursingTreatment->getTreatment() === $this) {
                $nursingTreatment->setTreatment(null);
            }
        }

        return $this;
    }
}
