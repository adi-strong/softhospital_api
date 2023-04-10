<?php

namespace App\Entity;

use App\AppTraits\CreatedAtTrait;
use App\Repository\InvoiceStoricRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: InvoiceStoricRepository::class)]
class InvoiceStoric
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read', 'nursing:read', 'invoice:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceStorics')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Invoice $invoice = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['invoice:read', 'nursing:read', 'invoice:read'])]
    private ?string $amount = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceStorics')]
    #[Groups(['invoice:read', 'nursing:read', 'invoice:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceStorics')]
    private ?Nursing $nursing = null;

    #[ORM\ManyToOne(inversedBy: 'invoiceStorics')]
    private ?CovenantInvoice $covenantInvoice = null;

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

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

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

    public function getNursing(): ?Nursing
    {
        return $this->nursing;
    }

    public function setNursing(?Nursing $nursing): self
    {
        $this->nursing = $nursing;

        return $this;
    }

    public function getCovenantInvoice(): ?CovenantInvoice
    {
        return $this->covenantInvoice;
    }

    public function setCovenantInvoice(?CovenantInvoice $covenantInvoice): self
    {
        $this->covenantInvoice = $covenantInvoice;

        return $this;
    }
}
