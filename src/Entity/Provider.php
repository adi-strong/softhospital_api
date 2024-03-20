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
use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Provider'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['provider:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
#[ApiFilter(SearchFilter::class, properties: ['wording' => 'ipartial'])]
class Provider
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['provider:read', 'supply:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le fournisseur doit être désigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['provider:read', 'supply:read'])]
    private ?string $wording = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le point focal doit être désigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['provider:read', 'supply:read'])]
    private ?string $focal = null;

    #[ORM\Column(length: 20)]
    #[Groups(['provider:read', 'supply:read'])]
    #[Assert\NotBlank(message: 'Le n° de téléphone doit être désigné.')]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Adresse email invalide.')]
    #[Groups(['provider:read', 'supply:read'])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'providers')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'providers')]
    #[Groups(['provider:read'])]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse doit être désignée.")]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['provider:read', 'supply:read'])]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: DrugstoreSupply::class)]
    private Collection $drugstoreSupplies;

    public function __construct()
    {
        $this->drugstoreSupplies = new ArrayCollection();
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

    public function getFocal(): ?string
    {
        return $this->focal;
    }

    public function setFocal(string $focal): self
    {
        $this->focal = $focal;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, DrugstoreSupply>
     */
    public function getDrugstoreSupplies(): Collection
    {
        return $this->drugstoreSupplies;
    }

    public function addDrugstoreSupply(DrugstoreSupply $drugstoreSupply): self
    {
        if (!$this->drugstoreSupplies->contains($drugstoreSupply)) {
            $this->drugstoreSupplies->add($drugstoreSupply);
            $drugstoreSupply->setProvider($this);
        }

        return $this;
    }

    public function removeDrugstoreSupply(DrugstoreSupply $drugstoreSupply): self
    {
        if ($this->drugstoreSupplies->removeElement($drugstoreSupply)) {
            // set the owning side to null (unless already changed)
            if ($drugstoreSupply->getProvider() === $this) {
                $drugstoreSupply->setProvider(null);
            }
        }

        return $this;
    }
}
