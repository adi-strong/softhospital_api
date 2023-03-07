<?php

namespace App\Entity;

use App\Repository\NursingMedicinesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NursingMedicinesRepository::class)]
class NursingMedicines
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'nursingMedicines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NursingTreatment $nursing = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'nursingMedicines')]
    private ?Medicine $medicine = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNursing(): ?NursingTreatment
    {
        return $this->nursing;
    }

    public function setNursing(?NursingTreatment $nursing): self
    {
        $this->nursing = $nursing;

        return $this;
    }

    public function getMedicine(): ?Medicine
    {
        return $this->medicine;
    }

    public function setMedicine(?Medicine $medicine): self
    {
        $this->medicine = $medicine;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
