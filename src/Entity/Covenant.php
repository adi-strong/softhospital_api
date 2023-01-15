<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\UIDTrait;
use App\Repository\CovenantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CovenantRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Covenant'],
  normalizationContext: ['groups' => ['covenant:read']],
  order: ['id' => 'DESC'],
)]
class Covenant
{
  use CreatedAtTrait, UIDTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['covenant:read', 'patient:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['covenant:read', 'patient:read'])]
    #[Assert\NotBlank(message: 'La dénomination doit être renseigné.')]
    private ?string $denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['covenant:read', 'patient:read'])]
    private ?string $unitName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['covenant:read', 'patient:read'])]
    #[Assert\NotBlank(message: 'Le point focal doit être renseigné.')]
    private ?string $focal = null;

    #[ORM\Column(length: 20)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'covenant', targetEntity: Patient::class)]
    private Collection $patients;

    public function __construct()
    {
        $this->patients = new ArrayCollection();
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
}
