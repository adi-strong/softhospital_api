<?php

namespace App\Entity;

use App\AppTraits\CreatedAtTrait;
use App\Repository\NursingTreatmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NursingTreatmentRepository::class)]
class NursingTreatment
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['nursing:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'nursingTreatments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Nursing $nursing = null;

    #[ORM\ManyToOne(inversedBy: 'nursingTreatments')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?Treatment $treatment = null;

    #[ORM\ManyToOne(inversedBy: 'nursingTreatments')]
    #[Groups(['nursing:read'])]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?array $medicines = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?\DateTimeInterface $leaveAt = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $price = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNursing(): ?Nursing
    {
        return $this->nursing;
    }

    public function setNursing(?Nursing $nursing): self
    {
        $this->nursing = $nursing;

        return $this;
    }

    public function getTreatment(): ?Treatment
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatment $treatment): self
    {
        $this->treatment = $treatment;

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

    public function getMedicines(): ?array
    {
        return $this->medicines;
    }

    public function setMedicines(?array $medicines): self
    {
        $this->medicines = $medicines;

        return $this;
    }

    public function getLeaveAt(): ?\DateTimeInterface
    {
        return $this->leaveAt;
    }

    public function setLeaveAt(?\DateTimeInterface $leaveAt): self
    {
        $this->leaveAt = $leaveAt;

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
}
