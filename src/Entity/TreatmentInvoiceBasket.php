<?php

namespace App\Entity;

use App\Repository\TreatmentInvoiceBasketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreatmentInvoiceBasketRepository::class)]
class TreatmentInvoiceBasket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'treatmentInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'treatmentInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Treatment $treatment = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

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
