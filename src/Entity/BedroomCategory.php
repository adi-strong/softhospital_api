<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\BedroomCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BedroomCategoryRepository::class)]
#[ApiResource(
  types: ['https://schema.org/BedroomCategory'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),
  ],
  normalizationContext: ['groups' => ['bedroomCategory:read']],
  order: ['id' => 'DESC'],
  forceEager: false,
  paginationEnabled: false,
)]
class BedroomCategory
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bedroomCategory:read', 'bedroom:read', 'bed:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['bedroomCategory:read', 'bedroom:read', 'bed:read', 'consult:read'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'bedroomCategories')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Bedroom::class)]
    private Collection $bedrooms;

    public function __construct()
    {
        $this->bedrooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, Bedroom>
     */
    public function getBedrooms(): Collection
    {
        return $this->bedrooms;
    }

    public function addBedroom(Bedroom $bedroom): self
    {
        if (!$this->bedrooms->contains($bedroom)) {
            $this->bedrooms->add($bedroom);
            $bedroom->setCategory($this);
        }

        return $this;
    }

    public function removeBedroom(Bedroom $bedroom): self
    {
        if ($this->bedrooms->removeElement($bedroom)) {
            // set the owning side to null (unless already changed)
            if ($bedroom->getCategory() === $this) {
                $bedroom->setCategory(null);
            }
        }

        return $this;
    }
}
