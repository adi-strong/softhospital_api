<?php

namespace App\Entity;

use App\Repository\ExamsInvoiceBasketRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamsInvoiceBasketRepository::class)]
class ExamsInvoiceBasket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'examsInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Invoice $invoice = null;

    #[ORM\ManyToOne(inversedBy: 'examsInvoiceBaskets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Exam $exam = null;

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

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

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
