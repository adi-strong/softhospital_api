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
use App\Repository\BedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BedRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Bed'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['bed:read']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['number' => 'ipartial'])]
class Bed
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['bed:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libellé du lit doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['bed:read', 'consult:read'])]
    private ?string $number = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['bed:read'])]
    private ?bool $itHasTaken = false;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: 'Le coût doit être renseigné.')]
    #[Groups(['bed:read'])]
    private ?string $cost = '0';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: 'Le prix doit être renseigné.')]
    #[Groups(['bed:read'])]
    private ?string $price = '0';

    #[ORM\ManyToOne(inversedBy: 'beds')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'beds')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotBlank(message: 'La chambre doit être renseignée.')]
    #[Assert\NotNull(message: 'Aucune valeur renseignée pour la chambre.')]
    #[Groups(['bed:read', 'consult:read'])]
    private ?Bedroom $bedroom = null;

    #[ORM\OneToMany(mappedBy: 'bed', targetEntity: Hospitalization::class)]
    private Collection $hospitalizations;

    public function __construct()
    {
        $this->hospitalizations = new ArrayCollection();
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

    public function isItHasTaken(): ?bool
    {
        return $this->itHasTaken;
    }

    public function setItHasTaken(?bool $itHasTaken): self
    {
        $this->itHasTaken = $itHasTaken;

        return $this;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

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

    public function getBedroom(): ?Bedroom
    {
        return $this->bedroom;
    }

    public function setBedroom(?Bedroom $bedroom): self
    {
        $this->bedroom = $bedroom;

        return $this;
    }

    /**
     * @return Collection<int, Hospitalization>
     */
    public function getHospitalizations(): Collection
    {
        return $this->hospitalizations;
    }

    public function addHospitalization(Hospitalization $hospitalization): self
    {
        if (!$this->hospitalizations->contains($hospitalization)) {
            $this->hospitalizations->add($hospitalization);
            $hospitalization->setBed($this);
        }

        return $this;
    }

    public function removeHospitalization(Hospitalization $hospitalization): self
    {
        if ($this->hospitalizations->removeElement($hospitalization)) {
            // set the owning side to null (unless already changed)
            if ($hospitalization->getBed() === $this) {
                $hospitalization->setBed(null);
            }
        }

        return $this;
    }
}
