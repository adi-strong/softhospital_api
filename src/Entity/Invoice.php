<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\IsDeletedTrait;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Invoice'],
    operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['invoice:read']],
  order: ['id' => 'DESC'],
)]
class Invoice
{
  use IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Ce champs doit être renseigné.')]
    #[Assert\NotNull(message: 'Le montant doit être renseigné.')]
    #[Groups(['invoice:read'])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $totalAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $paid = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $leftover = '0';

    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?bool $isComplete = false;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?\DateTimeInterface $releasedAt = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[Groups(['invoice:read'])]
    private ?Patient $patient = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist', 'remove'])]
    #[Groups(['invoice:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceStoric::class)]
    private Collection $invoiceStorics;

    public function __construct()
    {
        $this->invoiceStorics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPaid(): ?string
    {
        return $this->paid;
    }

    public function setPaid(?string $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getLeftover(): ?string
    {
        return $this->leftover;
    }

    public function setLeftover(?string $leftover): self
    {
        $this->leftover = $leftover;

        return $this;
    }

    public function isIsComplete(): ?bool
    {
        return $this->isComplete;
    }

    public function setIsComplete(bool $isComplete): self
    {
        $this->isComplete = $isComplete;

        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(?\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * @return Collection<int, InvoiceStoric>
     */
    public function getInvoiceStorics(): Collection
    {
        return $this->invoiceStorics;
    }

    public function addInvoiceStoric(InvoiceStoric $invoiceStoric): self
    {
        if (!$this->invoiceStorics->contains($invoiceStoric)) {
            $this->invoiceStorics->add($invoiceStoric);
            $invoiceStoric->setInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceStoric(InvoiceStoric $invoiceStoric): self
    {
        if ($this->invoiceStorics->removeElement($invoiceStoric)) {
            // set the owning side to null (unless already changed)
            if ($invoiceStoric->getInvoice() === $this) {
                $invoiceStoric->setInvoice(null);
            }
        }

        return $this;
    }
}
