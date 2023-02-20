<?php

namespace App\Entity;

use App\Repository\DrugstoreSupplyMedicineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DrugstoreSupplyMedicineRepository::class)]
class DrugstoreSupplyMedicine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['supply:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'drugstoreSupplyMedicines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DrugstoreSupply $drugstoreSupply = null;

    #[ORM\ManyToOne(inversedBy: 'drugstoreSupplyMedicines')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['supply:read'])]
    private ?Medicine $medicine = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['supply:read'])]
    private ?string $cost = null;

    #[ORM\Column]
    #[Groups(['supply:read'])]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supply:read'])]
    private ?\DateTimeInterface $expiryDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDrugstoreSupply(): ?DrugstoreSupply
    {
        return $this->drugstoreSupply;
    }

    public function setDrugstoreSupply(?DrugstoreSupply $drugstoreSupply): self
    {
        $this->drugstoreSupply = $drugstoreSupply;

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

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }
}
