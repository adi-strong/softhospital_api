<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\MedicineSubCategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicineSubCategoriesRepository::class)]
#[ApiResource(
  types: ['https://schema.org/MedicineSubCategories'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),
  ],
  normalizationContext: ['groups' => ['medSubCategory:read']],
  order: ['id' => 'DESC'],
)]
#[ApiResource(
  uriTemplate: '/medicine_categories/{id}/sub_categories',
  types: ['https://schema.org/MedicineSubCategories'],
  operations: [ new GetCollection() ],
  uriVariables: ['id' => new Link(fromProperty: 'medicineSubCategories', fromClass: MedicineCategories::class)],
  normalizationContext: ['groups' => ['medSubCategory:read']],
  paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['wording' => 'ipartial'])]
class MedicineSubCategories
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['medSubCategory:read', 'medCategory:read', 'medicine:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champs doit être renseigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['medSubCategory:read', 'medCategory:read', 'medicine:read'])]
    private ?string $wording = null;

    #[ORM\ManyToOne(inversedBy: 'medicineSubCategories')]
    #[Groups(['medSubCategory:read'])]
    private ?MedicineCategories $category = null;

    #[ORM\OneToMany(mappedBy: 'subCategory', targetEntity: Medicine::class)]
    private Collection $medicines;

    #[ORM\ManyToOne(inversedBy: 'medicineSubCategories')]
    private ?Hospital $hospital = null;

    public function __construct()
    {
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

    public function getCategory(): ?MedicineCategories
    {
        return $this->category;
    }

    public function setCategory(?MedicineCategories $category): self
    {
        $this->category = $category;

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
            $medicine->setSubCategory($this);
        }

        return $this;
    }

    public function removeMedicine(Medicine $medicine): self
    {
        if ($this->medicines->removeElement($medicine)) {
            // set the owning side to null (unless already changed)
            if ($medicine->getSubCategory() === $this) {
                $medicine->setSubCategory(null);
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
