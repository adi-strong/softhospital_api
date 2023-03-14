<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\LabResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LabResultRepository::class)]
#[ApiResource(
  types: ['https://schema.org/LabResult'],
  operations: [
    new GetCollection(),
    new Get(),
  ],
  normalizationContext: ['groups' => ['labResult:read']],
  order: ['id' => 'DESC'],
)]
class LabResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['labResult:read', 'lab:read', 'prescript:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'labResults')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lab $lab = null;

    #[ORM\ManyToOne(inversedBy: 'labResults')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['labResult:read', 'lab:read', 'prescript:read'])]
    private ?Exam $exam = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLab(): ?Lab
    {
        return $this->lab;
    }

    public function setLab(?Lab $lab): self
    {
        $this->lab = $lab;

        return $this;
    }

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }
}
