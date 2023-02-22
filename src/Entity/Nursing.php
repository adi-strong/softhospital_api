<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
use App\Repository\NursingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NursingRepository::class)]
#[ApiResource]
class Nursing
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'nursing', cascade: ['persist', 'remove'])]
    private ?Consultation $consultation = null;

    #[ORM\OneToMany(mappedBy: 'nursing', targetEntity: NursingTreatment::class, cascade: ['persist', 'remove'])]
    private Collection $nursingTreatments;

    public function __construct()
    {
        $this->nursingTreatments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConsultation(): ?Consultation
    {
        return $this->consultation;
    }

    public function setConsultation(?Consultation $consultation): self
    {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * @return Collection<int, NursingTreatment>
     */
    public function getNursingTreatments(): Collection
    {
        return $this->nursingTreatments;
    }

    public function addNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if (!$this->nursingTreatments->contains($nursingTreatment)) {
            $this->nursingTreatments->add($nursingTreatment);
            $nursingTreatment->setNursing($this);
        }

        return $this;
    }

    public function removeNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if ($this->nursingTreatments->removeElement($nursingTreatment)) {
            // set the owning side to null (unless already changed)
            if ($nursingTreatment->getNursing() === $this) {
                $nursingTreatment->setNursing(null);
            }
        }

        return $this;
    }
}
