<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\MedicineInvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicineInvoiceRepository::class)]
#[ApiResource(
  types: ['https://schema.org/MedicineInvoice'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
  ],
  normalizationContext: ['groups' => ['medicineInvoice:read']],
  order: ['id' => 'DESC'],
)]
class MedicineInvoice
{
  public ?array $values = [];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medicineInvoice:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant total doit être renseigné.')]
    #[Assert\NotNull(message: 'Le montant total doit être renseigné.')]
    #[Groups(['medicineInvoice:read'])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?\DateTimeInterface $released = null;

    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: MedicinesSold::class, cascade: ['persist'])]
    #[Groups(['medicineInvoice:read'])]
    private Collection $medicinesSolds;

    #[ORM\ManyToOne(inversedBy: 'medicineInvoices')]
    #[Groups(['medicineInvoice:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'medicineInvoices')]
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\NotBlank(message: 'La devise doit être renseignée.')]
    #[Groups(['medicineInvoice:read'])]
    private ?string $currency = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?float $discount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?string $totalAmount = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicineInvoice:read'])]
    private ?float $vTA = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'Le sous total doit être renseigné.')]
    #[Groups(['medicineInvoice:read'])]
    private ?string $subTotal = '0';

    public function __construct()
    {
        $this->medicinesSolds = new ArrayCollection();
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

    public function getReleased(): ?\DateTimeInterface
    {
        return $this->released;
    }

    public function setReleased(?\DateTimeInterface $released): self
    {
        $this->released = $released;

        return $this;
    }

    /**
     * @return Collection<int, MedicinesSold>
     */
    public function getMedicinesSolds(): Collection
    {
        return $this->medicinesSolds;
    }

    public function addMedicinesSold(MedicinesSold $medicinesSold): self
    {
        if (!$this->medicinesSolds->contains($medicinesSold)) {
            $this->medicinesSolds->add($medicinesSold);
            $medicinesSold->setInvoice($this);
        }

        return $this;
    }

    public function removeMedicinesSold(MedicinesSold $medicinesSold): self
    {
        if ($this->medicinesSolds->removeElement($medicinesSold)) {
            // set the owning side to null (unless already changed)
            if ($medicinesSold->getInvoice() === $this) {
                $medicinesSold->setInvoice(null);
            }
        }

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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

  #[Groups(['medicineInvoice:read'])]
  public function getInvoiceNumber(): string
  {
    return sprintf('%05d', $this->id);
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

  public function getDiscount(): ?float
  {
      return $this->discount;
  }

  public function setDiscount(?float $discount): self
  {
      $this->discount = $discount;

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

  public function getVTA(): ?float
  {
      return $this->vTA;
  }

  public function setVTA(?float $vTA): self
  {
      $this->vTA = $vTA;

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
}
