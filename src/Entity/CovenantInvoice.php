<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\CovenantInvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CovenantInvoiceRepository::class)]
#[ApiResource(
  types: ['https://schema.org/CovenantInvoice'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['invoice:read']],
  order: ['id' => 'DESC'],
)]
#[ApiResource(
  uriTemplate: '/covenants/{id}/covenant_invoices',
  types: ['https://schema.org/CovenantInvoice'],
  operations: [ new GetCollection() ],
  uriVariables: [ 'id' => new Link(fromProperty: 'covenantInvoices', fromClass: Covenant::class) ],
  normalizationContext: ['groups' => ['invoice:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: [ 'year' => 'exact', 'month' => 'exact' ])]
class CovenantInvoice
{
  public string $sum = '0';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['invoice:read'])]
    private ?string $amount = '0';

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

    #[ORM\Column(nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $vTA = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['invoice:read'])]
    private ?float $discount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?\DateTimeInterface $releasedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $fullName = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $subTotal = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $currency = null;

    #[ORM\Column]
    #[Groups(['invoice:read'])]
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'covenantInvoices')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'covenantInvoices')]
    #[Groups(['invoice:read'])]
    private ?Covenant $covenant = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['invoice:read'])]
    private ?int $year = null;

    #[ORM\Column(length: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $month = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $filesPrice = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $totalActsBaskets = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $totalExamsBaskets = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $totalNursingPrice = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['invoice:read'])]
    private ?string $hospPrice = '0';

    #[ORM\ManyToOne(inversedBy: 'covenantInvoices')]
    #[Groups(['invoice:read'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'covenantInvoice', targetEntity: InvoiceStoric::class)]
    #[Groups(['invoice:read'])]
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

    public function getVTA(): ?float
    {
        return $this->vTA;
    }

    public function setVTA(?float $vTA): self
    {
        $this->vTA = $vTA;

        return $this;
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

    public function getReleasedAt(): ?\DateTimeInterface
    {
        return $this->releasedAt;
    }

    public function setReleasedAt(?\DateTimeInterface $releasedAt): self
    {
        $this->releasedAt = $releasedAt;

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

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(?string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

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

    public function getCovenant(): ?Covenant
    {
        return $this->covenant;
    }

    public function setCovenant(?Covenant $covenant): self
    {
        $this->covenant = $covenant;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(?string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getFilesPrice(): ?string
    {
        return $this->filesPrice;
    }

    public function setFilesPrice(?string $filesPrice): self
    {
        $this->filesPrice = $filesPrice;

        return $this;
    }

    public function getTotalActsBaskets(): ?string
    {
        return $this->totalActsBaskets;
    }

    public function setTotalActsBaskets(?string $totalActsBaskets): self
    {
        $this->totalActsBaskets = $totalActsBaskets;

        return $this;
    }

    public function getTotalExamsBaskets(): ?string
    {
        return $this->totalExamsBaskets;
    }

    public function setTotalExamsBaskets(?string $totalExamsBaskets): self
    {
        $this->totalExamsBaskets = $totalExamsBaskets;

        return $this;
    }

    public function getTotalNursingPrice(): ?string
    {
        return $this->totalNursingPrice;
    }

    public function setTotalNursingPrice(?string $totalNursingPrice): self
    {
        $this->totalNursingPrice = $totalNursingPrice;

        return $this;
    }

    public function getHospPrice(): ?string
    {
        return $this->hospPrice;
    }

    public function setHospPrice(?string $hospPrice): self
    {
        $this->hospPrice = $hospPrice;

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
            $invoiceStoric->setCovenantInvoice($this);
        }

        return $this;
    }

    public function removeInvoiceStoric(InvoiceStoric $invoiceStoric): self
    {
        if ($this->invoiceStorics->removeElement($invoiceStoric)) {
            // set the owning side to null (unless already changed)
            if ($invoiceStoric->getCovenantInvoice() === $this) {
                $invoiceStoric->setCovenantInvoice(null);
            }
        }

        return $this;
    }
}
