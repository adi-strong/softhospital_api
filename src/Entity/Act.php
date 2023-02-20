<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\ActRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Act'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['act:read']],
  order: ['id' => 'DESC'],
)]
class Act
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['act:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libellé doit être rensigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit contenir au moins 2 caractères.',
      maxMessage: 'Ce champs ne peut dépasser 255 caractères.'
    )]
    #[Groups(['act:read'])]
    private ?string $wording = null;

    #[ORM\ManyToOne(inversedBy: 'acts')]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'acts')]
    #[Groups(['act:read'])]
    private ?ActCategory $category = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: 'Le prix doit être renseigné.')]
    #[Groups(['act:read'])]
    private ?string $price = '0';

    #[ORM\ManyToMany(targetEntity: Consultation::class, mappedBy: 'acts')]
    private Collection $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;

        return $this;
    }

    public function getCategory(): ?ActCategory
    {
        return $this->category;
    }

    public function setCategory(?ActCategory $category): self
    {
        $this->category = $category;

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

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->addAct($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            $consultation->removeAct($this);
        }

        return $this;
    }
}
