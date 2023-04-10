<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\CovenantRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: CovenantRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Covenant'],
  operations: [
    new GetCollection(),
    new Post(inputFormats: ['multipart' => ['multipart/form-data']]),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['covenant:read']],
  denormalizationContext: ['groups' => ['covenant:write']],
  order: ['id' => 'DESC'],
)]
#[ApiFilter(SearchFilter::class, properties: ['denomination' => 'ipartial'])]
class Covenant
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['covenant:read', 'patient:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'pdf_obj', fileNameProperty: 'filePath')]
    #[Groups(['covenant:write'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['covenant:read', 'patient:read', 'invoice:read'])]
    public ?string $filePath = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['covenant:read', 'patient:read', 'consult:read', 'invoice:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['covenant:read', 'covenant:write', 'patient:read', 'consult:read', 'invoice:read'])]
    #[Assert\NotBlank(message: 'La dénomination doit être renseigné.')]
    private ?string $denomination = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Ce champs ne peut dépasser 20 caractères.')]
    #[Groups(['covenant:read', 'covenant:write', 'patient:read'])]
    private ?string $unitName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le point focal doit être renseigné.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['covenant:read', 'covenant:write'])]
    private ?string $focal = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Le n° de téléphone doit être renseigné.')]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Groups(['covenant:read', 'covenant:write'])]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: "L'adresse email doit être renseignée.")]
    #[Assert\NotNull(message: 'Ce champs doit être renseigné.')]
    #[Assert\Email(message: 'Adresse email invalide.')]
    #[Groups(['covenant:read', 'covenant:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, maxMessage: 'Ce champs ne peut dépasser 255 caractères.')]
    #[Groups(['covenant:read', 'covenant:write', 'patient:read'])]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'covenant', targetEntity: Patient::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', unique: false)]
    #[Groups(['covenant:read'])]
    private Collection $patients;

    #[ORM\ManyToOne(inversedBy: 'covenants')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'covenants')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['covenant:read', 'covenant:write', 'patient:read'])]
    private ?ImageObject $logo = null;

    #[ORM\OneToMany(mappedBy: 'covenant', targetEntity: CovenantInvoice::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', unique: false)]
    private Collection $covenantInvoices;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
        $this->covenantInvoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getUnitName(): ?string
    {
        return $this->unitName;
    }

    public function setUnitName(?string $unitName): self
    {
        $this->unitName = $unitName;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Patient>
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients->add($patient);
            $patient->setCovenant($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getCovenant() === $this) {
                $patient->setCovenant(null);
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

    public function getLogo(): ?ImageObject
    {
        return $this->logo;
    }

    public function setLogo(?ImageObject $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

  #[Groups(['covenant:read', 'patient:read'])]
  public function getSlug(): string
  {
    return (new Slugify())->slugify($this->denomination);
  }

  /**
   * @return Collection<int, CovenantInvoice>
   */
  public function getCovenantInvoices(): Collection
  {
      return $this->covenantInvoices;
  }

  public function addCovenantInvoice(CovenantInvoice $covenantInvoice): self
  {
      if (!$this->covenantInvoices->contains($covenantInvoice)) {
          $this->covenantInvoices->add($covenantInvoice);
          $covenantInvoice->setCovenant($this);
      }

      return $this;
  }

  public function removeCovenantInvoice(CovenantInvoice $covenantInvoice): self
  {
      if ($this->covenantInvoices->removeElement($covenantInvoice)) {
          // set the owning side to null (unless already changed)
          if ($covenantInvoice->getCovenant() === $this) {
              $covenantInvoice->setCovenant(null);
          }
      }

      return $this;
  }
}
