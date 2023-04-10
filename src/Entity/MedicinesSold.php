<?php

namespace App\Entity;

use App\Repository\MedicinesSoldRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicinesSoldRepository::class)]
class MedicinesSold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medicineInvoice:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La quantité doit être renseignée.')]
    #[Assert\NotNull(message: 'La quantité doit être renseignée.')]
    #[Groups(['medicineInvoice:read'])]
    private ?float $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le prix doit être renseigné.')]
    #[Assert\NotNull(message: 'Le prix doit être renseigné.')]
    #[Groups(['medicineInvoice:read'])]
    private ?string $price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?string $sum = null;

    #[ORM\ManyToOne(inversedBy: 'medicinesSolds')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['medicineInvoice:read'])]
    private ?Medicine $medicine = null;

    #[ORM\ManyToOne(inversedBy: 'medicinesSolds')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MedicineInvoice $invoice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?string $cost = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?string $gain = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSum(): ?string
    {
        return $this->sum;
    }

    public function setSum(?string $sum): self
    {
        $this->sum = $sum;

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

    public function getInvoice(): ?MedicineInvoice
    {
        return $this->invoice;
    }

    public function setInvoice(?MedicineInvoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getGain(): ?string
    {
        return $this->gain;
    }

    public function setGain(?string $gain): self
    {
        $this->gain = $gain;

        return $this;
    }
}
