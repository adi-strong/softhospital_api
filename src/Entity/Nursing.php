<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use App\AppTraits\CreatedAtTrait;
use App\Repository\NursingRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NursingRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Prescription'],
  operations: [
    new GetCollection(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['nursing:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['fullName' => 'ipartial'])]
class Nursing
{
  use CreatedAtTrait;

  public ?array $treatments = [];

  public ?array $actsItems = null;

  public ?DateTime $arrivedAt = null;

  public ?DateTime $leaveAt = null;

  public ?array $treatmentValues = null;

  public ?string $sum = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['nursing:read', 'invoice:read', 'invoice:read'])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'nursing')]
    #[Groups(['nursing:read'])]
    private ?Consultation $consultation = null;

    #[ORM\OneToMany(mappedBy: 'nursing', targetEntity: NursingTreatment::class, cascade: ['remove', 'persist'])]
    #[Groups(['nursing:read', 'invoice:read'])]
    private Collection $nursingTreatments;

    #[ORM\ManyToOne(inversedBy: 'nursings')]
    #[Groups(['nursing:read'])]
    private ?Patient $patient = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?bool $isPublished = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'nursings')]
    private ?Hospital $hospital = null;

    #[ORM\Column]
    #[Groups(['nursing:read'])]
    private ?bool $isCompleted = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $amount = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $totalAmount = '0';

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?float $discount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?float $vTA = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $paid = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $leftover = '0';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $fullName = null;

    #[ORM\OneToMany(mappedBy: 'nursing', targetEntity: InvoiceStoric::class)]
    private Collection $invoiceStorics;

    #[ORM\Column(nullable: true)]
    private array $arrivalDates = [];

    #[ORM\Column(nullable: true)]
    private array $releasedAtItems = [];

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $subTotal = '0';

    #[ORM\Column]
    #[Groups(['nursing:read'])]
    private ?bool $isPayed = false;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['nursing:read'])]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['nursing:read'])]
    private ?array $acts = [];

    public function __construct()
    {
        $this->nursingTreatments = new ArrayCollection();
        $this->invoiceStorics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * @return Collection<int, NursingTreatment>
     */
    public function getNursingTreatments(): Collection
    {
        return $this->nursingTreatments;
    }

    public function addNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if (!$this->nursingTreatments->contains($nursingTreatment)) {
            $this->nursingTreatments->add($nursingTreatment);
            $nursingTreatment->setNursing($this);
        }

        return $this;
    }

    public function removeNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if ($this->nursingTreatments->removeElement($nursingTreatment)) {
            // set the owning side to null (unless already changed)
            if ($nursingTreatment->getNursing() === $this) {
                $nursingTreatment->setNursing(null);
            }
        }

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

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

  #[Groups(['nursing:read'])]
  public function getNursingNumber(): ?string
  {
    return sprintf('%05d', $this->id);
  }

  public function isIsCompleted(): ?bool
  {
      return $this->isCompleted;
  }

  public function setIsCompleted(bool $isCompleted): self
  {
      $this->isCompleted = $isCompleted;

      return $this;
  }

  public function getAmount(): ?string
  {
      return $this->amount;
  }

  public function setAmount(?string $amount): self
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

  public function getFullName(): ?string
  {
      return $this->fullName;
  }

  public function setFullName(?string $fullName): self
  {
      $this->fullName = $fullName;

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
          $invoiceStoric->setNursing($this);
      }

      return $this;
  }

  public function removeInvoiceStoric(InvoiceStoric $invoiceStoric): self
  {
      if ($this->invoiceStorics->removeElement($invoiceStoric)) {
          // set the owning side to null (unless already changed)
          if ($invoiceStoric->getNursing() === $this) {
              $invoiceStoric->setNursing(null);
          }
      }

      return $this;
  }

  public function getArrivalDates(): array
  {
      return $this->arrivalDates;
  }

  public function setArrivalDates(?array $arrivalDates): self
  {
      $this->arrivalDates = $arrivalDates;

      return $this;
  }

  public function getReleasedAtItems(): array
  {
      return $this->releasedAtItems;
  }

  public function setReleasedAtItems(?array $releasedAtItems): self
  {
      $this->releasedAtItems = $releasedAtItems;

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

  public function isIsPayed(): ?bool
  {
      return $this->isPayed;
  }

  public function setIsPayed(bool $isPayed): self
  {
      $this->isPayed = $isPayed;

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

  public function getActs(): ?array
  {
      return $this->acts;
  }

  public function setActs(?array $acts): self
  {
      $this->acts = $acts;

      return $this;
  }
}
