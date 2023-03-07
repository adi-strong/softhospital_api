<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\LabResultRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LabResultRepository::class)]
#[ApiResource]
class LabResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'labResults')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Lab $lab = null;

    #[ORM\ManyToOne(inversedBy: 'labResults')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Exam $exam = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $results = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

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

    public function getResults(): ?string
    {
        return $this->results;
    }

    public function setResults(?string $results): self
    {
        $this->results = $results;

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
}
