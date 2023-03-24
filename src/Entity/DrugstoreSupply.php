<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\DrugstoreSupplyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DrugstoreSupplyRepository::class)]
#[ApiResource(
  types: ['https://schema.org/DrugstoreSupply'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
  ],
  normalizationContext: ['groups' => ['supply:read']],
  order: ['id' => 'DESC'],
)]
#[UniqueEntity('document', message: "Ce n° de document existe déjà.")]
class DrugstoreSupply
{
    #[Assert\NotBlank(message: 'Aucun produit renseigné.')]
    public array $values = [];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['supply:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le n° du document doit être renseigné.')]
    #[Groups(['supply:read'])]
    private ?string $document = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['supply:read'])]
    private ?\DateTimeInterface $released = null;

    #[ORM\ManyToOne(inversedBy: 'drugstoreSupplies')]
    #[Groups(['supply:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'drugstoreSupplies')]
    private ?Hospital $hospital = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Le montant de la facture est inconnu.')]
    #[Groups(['supply:read'])]
    private ?string $subTotal = null;

    #[ORM\ManyToOne(inversedBy: 'drugstoreSupplies')]
    #[Groups(['supply:read'])]
    private ?Provider $provider = null;

    #[ORM\OneToMany(mappedBy: 'drugstoreSupply', targetEntity: DrugstoreSupplyMedicine::class, cascade: ['persist'])]
    #[Groups(['supply:read'])]
    private Collection $drugstoreSupplyMedicines;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'La devise doit être renseignée.')]
    #[Groups(['supply:read'])]
    private ?string $currency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'Le montant total de la facture est inconnu.')]
    #[Groups(['supply:read'])]
    private ?string $total = null;

    public function __construct()
    {
        $this->drugstoreSupplyMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): self
    {
        $this->document = $document;

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

    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    public function setSubTotal(string $subTotal): self
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

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
            $drugstoreSupplyMedicine->setDrugstoreSupply($this);
        }

        return $this;
    }

    public function removeDrugstoreSupplyMedicine(DrugstoreSupplyMedicine $drugstoreSupplyMedicine): self
    {
        if ($this->drugstoreSupplyMedicines->removeElement($drugstoreSupplyMedicine)) {
            // set the owning side to null (unless already changed)
            if ($drugstoreSupplyMedicine->getDrugstoreSupply() === $this) {
                $drugstoreSupplyMedicine->setDrugstoreSupply(null);
            }
        }

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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }
}
