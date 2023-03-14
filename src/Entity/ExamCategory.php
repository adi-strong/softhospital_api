<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\ExamCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExamCategoryRepository::class)]
#[ApiResource(
  types: ['https://schema.org/ExamCategory'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),
  ],
  normalizationContext: ['groups' => ['examCategory:read']],
  order: ['id' => 'DESC'],
  paginationEnabled: false,
)]
class ExamCategory
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['examCategory:read', 'exam:read', 'lab:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['examCategory:read', 'exam:read', 'lab:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'examCategories')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Exam::class)]
    private Collection $exams;

    public function __construct()
    {
        $this->exams = new ArrayCollection();
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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

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
            $exam->setCategory($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getCategory() === $this) {
                $exam->setCategory(null);
            }
        }

        return $this;
    }
}
