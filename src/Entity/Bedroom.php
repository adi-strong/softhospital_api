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
use App\Repository\BedroomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BedroomRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Bedroom'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['bedroom:read']],
  order: ['id' => 'DESC'],
  forceEager: false,
  paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['number' => 'ipartial'])]
class Bedroom
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bedroom:read', 'bed:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro de chambre doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['bedroom:read', 'bed:read', 'consult:read'])]
    private ?string $number = null;

    #[ORM\ManyToOne(inversedBy: 'bedrooms')]
    #[Groups(['bedroom:read', 'bed:read', 'consult:read'])]
    private ?BedroomCategory $category = null;

    #[ORM\ManyToOne(inversedBy: 'bedrooms')]
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 53, nullable: true)]
    #[Assert\Length(max: 53, maxMessage: 'Ce champs ne peut dépasser 53 caractères.')]
    #[Groups(['bedroom:read', 'bed:read'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'bedroom', targetEntity: Bed::class, orphanRemoval: true)]
    private Collection $beds;

    public function __construct()
    {
        $this->beds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCategory(): ?BedroomCategory
    {
        return $this->category;
    }

    public function setCategory(?BedroomCategory $category): self
    {
        $this->category = $category;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Bed>
     */
    public function getBeds(): Collection
    {
        return $this->beds;
    }

    public function addBed(Bed $bed): self
    {
        if (!$this->beds->contains($bed)) {
            $this->beds->add($bed);
            $bed->setBedroom($this);
        }

        return $this;
    }

    public function removeBed(Bed $bed): self
    {
        if ($this->beds->removeElement($bed)) {
            // set the owning side to null (unless already changed)
            if ($bed->getBedroom() === $this) {
                $bed->setBedroom(null);
            }
        }

        return $this;
    }
}
