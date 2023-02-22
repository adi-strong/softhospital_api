<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\ExamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExamRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Exam'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['exam:read']],
  order: ['id' => 'DESC'],
)]
class Exam
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['exam:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libellé doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['exam:read'])]
    private ?string $wording = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'exams')]
    #[Groups(['exam:read'])]
    private ?ExamCategory $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'Le prix doit être renseigné.')]
    #[Groups(['exam:read'])]
    private ?string $price = '0';

    #[ORM\ManyToMany(targetEntity: Consultation::class, mappedBy: 'exams')]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: ExamsInvoiceBasket::class)]
    private Collection $examsInvoiceBaskets;

    #[ORM\OneToMany(mappedBy: 'exam', targetEntity: LabResult::class, cascade: ['persist'])]
    private Collection $labResults;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->examsInvoiceBaskets = new ArrayCollection();
        $this->labResults = new ArrayCollection();
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

    public function getCategory(): ?ExamCategory
    {
        return $this->category;
    }

    public function setCategory(?ExamCategory $category): self
    {
        $this->category = $category;

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
            $consultation->addExam($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            $consultation->removeExam($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ExamsInvoiceBasket>
     */
    public function getExamsInvoiceBaskets(): Collection
    {
        return $this->examsInvoiceBaskets;
    }

    public function addExamsInvoiceBasket(ExamsInvoiceBasket $examsInvoiceBasket): self
    {
        if (!$this->examsInvoiceBaskets->contains($examsInvoiceBasket)) {
            $this->examsInvoiceBaskets->add($examsInvoiceBasket);
            $examsInvoiceBasket->setExam($this);
        }

        return $this;
    }

    public function removeExamsInvoiceBasket(ExamsInvoiceBasket $examsInvoiceBasket): self
    {
        if ($this->examsInvoiceBaskets->removeElement($examsInvoiceBasket)) {
            // set the owning side to null (unless already changed)
            if ($examsInvoiceBasket->getExam() === $this) {
                $examsInvoiceBasket->setExam(null);
            }
        }

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
            $labResult->setExam($this);
        }

        return $this;
    }

    public function removeLabResult(LabResult $labResult): self
    {
        if ($this->labResults->removeElement($labResult)) {
            // set the owning side to null (unless already changed)
            if ($labResult->getExam() === $this) {
                $labResult->setExam(null);
            }
        }

        return $this;
    }
}
