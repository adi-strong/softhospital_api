<?php

namespace App\Entity;

use App\Repository\ActsInvoiceBasketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActsInvoiceBasketRepository::class)]
class ActsInvoiceBasket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'actsInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'actsInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Act $act = null;

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

    public function getAct(): ?Act
    {
        return $this->act;
    }

    public function setAct(?Act $act): self
    {
        $this->act = $act;

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
