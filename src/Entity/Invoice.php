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
use Symfony\Component\Security\Core\User\UserInterface;
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

  public ?string $sum = '0';

  public ?bool $isBedroomLeaved = false;

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
    private ?string $totalAmount = '0';

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
    #[Assert\NotBlank(message: 'Ce champs doit être renseigné.')]
    #[Assert\NotNull(message: 'Le patient doit être renseigné.')]
    #[Groups(['invoice:read'])]
    private ?Patient $patient = null;

    #[ORM\OneToOne(inversedBy: 'invoice', cascade: ['persist'])]
    #[Groups(['invoice:read'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Consultation $consultation = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceStoric::class)]
    private Collection $invoiceStorics;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: ActsInvoiceBasket::class, cascade: ['persist', 'remove'])]
    #[Groups(['invoice:read'])]
    private Collection $actsInvoiceBaskets;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: ExamsInvoiceBasket::class, cascade: ['persist', 'remove'])]
    #[Groups(['invoice:read'])]
    private Collection $examsInvoiceBaskets;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $hospitalizationAmount = '0';

    #[ORM\Column(nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $discount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $vTA = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $fullName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->invoiceStorics = new ArrayCollection();
        $this->actsInvoiceBaskets = new ArrayCollection();
        $this->examsInvoiceBaskets = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ActsInvoiceBasket>
     */
    public function getActsInvoiceBaskets(): Collection
    {
        return $this->actsInvoiceBaskets;
    }

    public function addActsInvoiceBasket(ActsInvoiceBasket $actsInvoiceBasket): self
    {
        if (!$this->actsInvoiceBaskets->contains($actsInvoiceBasket)) {
            $this->actsInvoiceBaskets->add($actsInvoiceBasket);
            $actsInvoiceBasket->setInvoice($this);
        }

        return $this;
    }

    public function removeActsInvoiceBasket(ActsInvoiceBasket $actsInvoiceBasket): self
    {
        if ($this->actsInvoiceBaskets->removeElement($actsInvoiceBasket)) {
            // set the owning side to null (unless already changed)
            if ($actsInvoiceBasket->getInvoice() === $this) {
                $actsInvoiceBasket->setInvoice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExamsInvoiceBasket>
     */
    public function getExamsInvoiceBaskets(): Collection
    {
        return $this->examsInvoiceBaskets;
    }

    public function addExamsInvoiceBasket(ExamsInvoiceBasket $examsInvoiceBasket): self
    {
        if (!$this->examsInvoiceBaskets->contains($examsInvoiceBasket)) {
            $this->examsInvoiceBaskets->add($examsInvoiceBasket);
            $examsInvoiceBasket->setInvoice($this);
        }

        return $this;
    }

    public function removeExamsInvoiceBasket(ExamsInvoiceBasket $examsInvoiceBasket): self
    {
        if ($this->examsInvoiceBaskets->removeElement($examsInvoiceBasket)) {
            // set the owning side to null (unless already changed)
            if ($examsInvoiceBasket->getInvoice() === $this) {
                $examsInvoiceBasket->setInvoice(null);
            }
        }

        return $this;
    }

    public function getHospitalizationAmount(): ?string
    {
        return $this->hospitalizationAmount;
    }

    public function setHospitalizationAmount(?string $hospitalizationAmount): self
    {
        $this->hospitalizationAmount = $hospitalizationAmount;

        return $this;
    }

  #[Groups(['invoice:read'])]
  public function getTotalSum(): ?string
  {
    $hospAmount = $this->hospitalizationAmount;
    $amount = $this->amount + $hospAmount;

    if (null !== $this->vTA && null !== $this->discount) {
      $vTA = ($amount * $this->vTA) / 100;
      $discount = ($amount * $this->discount) / 100;
      $total = ($amount + $vTA) + ($amount - $discount);
    }
    elseif (null !== $this->discount) {
      $discount = ($amount * $this->discount) / 100;
      $total = $amount - $discount;
    }
    elseif (null !== $this->vTA) {
      $vTA = ($amount * $this->vTA) / 100;
      $total = $amount + $vTA;
    }
    else $total = $amount;

    return round($total, 2);
  }

  #[Groups(['invoice:read'])]
  public function getInvoiceNumber(): ?string
  {
    return sprintf('%05d', $this->id);
  }

  public function getDiscount(): ?float
  {
      return $this->discount;
  }

  public function setDiscount(?float $discount): self
  {
      $this->discount = $discount;

      return $this;
  }

  public function getVTA(): ?float
  {
      return $this->vTA;
  }

  public function setVTA(?float $vTA): self
  {
      $this->vTA = $vTA;

      return $this;
  }

  public function getFullName(): ?string
  {
      return $this->fullName;
  }

  public function setFullName(?string $fullName): self
  {
      $this->fullName = $fullName;

      return $this;
  }

  public function getUpdatedAt(): ?\DateTimeInterface
  {
      return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
  {
      $this->updatedAt = $updatedAt;

      return $this;
  }
}
