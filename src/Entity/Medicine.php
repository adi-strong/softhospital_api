<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Controller\DestockingMedicinePublication;
use App\Repository\MedicineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicineRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Medicine'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['medicine:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['wording' => 'ipartial', 'code' => 'ipartial'])]
class Medicine
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medicine:read', 'supply:read', 'medicineInvoice:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['medicine:read'])]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le produit doit être désigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['medicine:read', 'supply:read', 'medicineInvoice:read'])]
    private ?string $wording = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicine:read'])]
    private ?string $cost = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['medicine:read'])]
    private ?string $price = '0';

    #[ORM\Column(nullable: true)]
    #[Groups(['medicine:read'])]
    private ?float $quantity = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['medicine:read'])]
    private ?\DateTimeInterface $expiryDate = null;

    #[ORM\ManyToOne(inversedBy: 'medicines')]
    #[Groups(['medicine:read', 'supply:read', 'medicineInvoice:read'])]
    private ?ConsumptionUnit $consumptionUnit = null;

    #[ORM\ManyToOne(inversedBy: 'medicines')]
    #[Groups(['medicine:read'])]
    private ?MedicineCategories $category = null;

    #[ORM\ManyToOne(inversedBy: 'medicines')]
    #[Groups(['medicine:read'])]
    private ?MedicineSubCategories $subCategory = null;

    #[ORM\ManyToOne(inversedBy: 'medicines')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'medicines')]
    #[Groups(['medicine:read'])]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicine:read'])]
    private ?int $daysRemainder = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['medicine:read'])]
    private ?\DateTimeInterface $released = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicine:read'])]
    private ?float $totalQuantity = 0;

    #[ORM\OneToMany(mappedBy: 'medicine', targetEntity: DrugstoreSupplyMedicine::class, cascade: ['persist'])]
    private Collection $drugstoreSupplyMedicines;

    #[ORM\OneToMany(mappedBy: 'medicine', targetEntity: MedicinesSold::class)]
    private Collection $medicinesSolds;

    #[ORM\Column(nullable: true)]
    #[Groups(['medicine:read'])]
    private ?float $vTA = null;

    #[ORM\OneToMany(mappedBy: 'medicine', targetEntity: DestockingOfMedicines::class)]
    #[Groups(['medicine:read'])]
    private Collection $destockingOfMedicines;

    public function __construct()
    {
        $this->drugstoreSupplyMedicines = new ArrayCollection();
        $this->medicinesSolds = new ArrayCollection();
        $this->destockingOfMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getExpiryDate(): ?\DateTimeInterface
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(?\DateTimeInterface $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getConsumptionUnit(): ?ConsumptionUnit
    {
        return $this->consumptionUnit;
    }

    public function setConsumptionUnit(?ConsumptionUnit $consumptionUnit): self
    {
        $this->consumptionUnit = $consumptionUnit;

        return $this;
    }

    public function getCategory(): ?MedicineCategories
    {
        return $this->category;
    }

    public function setCategory(?MedicineCategories $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSubCategory(): ?MedicineSubCategories
    {
        return $this->subCategory;
    }

    public function setSubCategory(?MedicineSubCategories $subCategory): self
    {
        $this->subCategory = $subCategory;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDaysRemainder(): ?int
    {
        return $this->daysRemainder;
    }

    public function setDaysRemainder(?int $daysRemainder): self
    {
        $this->daysRemainder = $daysRemainder;

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

    public function getTotalQuantity(): ?float
    {
        return $this->totalQuantity;
    }

    public function setTotalQuantity(?float $totalQuantity): self
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    /**
     * @return Collection<int, DrugstoreSupplyMedicine>
     */
    public function getDrugstoreSupplyMedicines(): Collection
    {
        return $this->drugstoreSupplyMedicines;
    }

    public function addDrugstoreSupplyMedicine(DrugstoreSupplyMedicine $drugstoreSupplyMedicine): self
    {
        if (!$this->drugstoreSupplyMedicines->contains($drugstoreSupplyMedicine)) {
            $this->drugstoreSupplyMedicines->add($drugstoreSupplyMedicine);
            $drugstoreSupplyMedicine->setMedicine($this);
        }

        return $this;
    }

    public function removeDrugstoreSupplyMedicine(DrugstoreSupplyMedicine $drugstoreSupplyMedicine): self
    {
        if ($this->drugstoreSupplyMedicines->removeElement($drugstoreSupplyMedicine)) {
            // set the owning side to null (unless already changed)
            if ($drugstoreSupplyMedicine->getMedicine() === $this) {
                $drugstoreSupplyMedicine->setMedicine(null);
            }
        }

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
            $medicinesSold->setMedicine($this);
        }

        return $this;
    }

    public function removeMedicinesSold(MedicinesSold $medicinesSold): self
    {
        if ($this->medicinesSolds->removeElement($medicinesSold)) {
            // set the owning side to null (unless already changed)
            if ($medicinesSold->getMedicine() === $this) {
                $medicinesSold->setMedicine(null);
            }
        }

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

    /**
     * @return Collection<int, DestockingOfMedicines>
     */
    public function getDestockingOfMedicines(): Collection
    {
        return $this->destockingOfMedicines;
    }

    public function addDestockingOfMedicine(DestockingOfMedicines $destockingOfMedicine): self
    {
        if (!$this->destockingOfMedicines->contains($destockingOfMedicine)) {
            $this->destockingOfMedicines->add($destockingOfMedicine);
            $destockingOfMedicine->setMedicine($this);
        }

        return $this;
    }

    public function removeDestockingOfMedicine(DestockingOfMedicines $destockingOfMedicine): self
    {
        if ($this->destockingOfMedicines->removeElement($destockingOfMedicine)) {
            // set the owning side to null (unless already changed)
            if ($destockingOfMedicine->getMedicine() === $this) {
                $destockingOfMedicine->setMedicine(null);
            }
        }

        return $this;
    }
}
