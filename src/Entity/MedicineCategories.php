<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\MedicineCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicineCategoriesRepository::class)]
#[ApiResource(
  types: ['https://schema.org/MedicineCategories'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),
  ],
  normalizationContext: ['groups' => ['medCategory:read']],
  order: ['id' => 'DESC'],
  paginationEnabled: false,
)]
class MedicineCategories
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medCategory:read', 'medicine:read', 'medSubCategory:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Vous devez nommer cette catégorie.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['medCategory:read', 'medicine:read', 'medSubCategory:read'])]
    private ?string $wording = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: MedicineSubCategories::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', unique: false)]
    #[Groups(['medCategory:read'])]
    private Collection $medicineSubCategories;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Medicine::class)]
    private Collection $medicines;

    #[ORM\ManyToOne(inversedBy: 'medicineCategories')]
    private ?Hospital $hospital = null;

    public function __construct()
    {
        $this->medicineSubCategories = new ArrayCollection();
        $this->medicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, MedicineSubCategories>
     */
    public function getMedicineSubCategories(): Collection
    {
        return $this->medicineSubCategories;
    }

    public function addMedicineSubCategory(MedicineSubCategories $medicineSubCategory): self
    {
        if (!$this->medicineSubCategories->contains($medicineSubCategory)) {
            $this->medicineSubCategories->add($medicineSubCategory);
            $medicineSubCategory->setCategory($this);
        }

        return $this;
    }

    public function removeMedicineSubCategory(MedicineSubCategories $medicineSubCategory): self
    {
        if ($this->medicineSubCategories->removeElement($medicineSubCategory)) {
            // set the owning side to null (unless already changed)
            if ($medicineSubCategory->getCategory() === $this) {
                $medicineSubCategory->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Medicine>
     */
    public function getMedicines(): Collection
    {
        return $this->medicines;
    }

    public function addMedicine(Medicine $medicine): self
    {
        if (!$this->medicines->contains($medicine)) {
            $this->medicines->add($medicine);
            $medicine->setCategory($this);
        }

        return $this;
    }

    public function removeMedicine(Medicine $medicine): self
    {
        if ($this->medicines->removeElement($medicine)) {
            // set the owning side to null (unless already changed)
            if ($medicine->getCategory() === $this) {
                $medicine->setCategory(null);
            }
        }

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
}
