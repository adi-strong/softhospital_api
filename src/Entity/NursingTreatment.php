<?php

namespace App\Entity;

use App\Repository\NursingTreatmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NursingTreatmentRepository::class)]
class NursingTreatment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'nursingTreatments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nursing $nursing = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'nursingTreatments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Treatment $treatment = null;

    #[ORM\OneToMany(mappedBy: 'nursing', targetEntity: NursingMedicines::class, cascade: ['persist', 'remove'])]
    private Collection $nursingMedicines;

    public function __construct()
    {
        $this->nursingMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNursing(): ?Nursing
    {
        return $this->nursing;
    }

    public function setNursing(?Nursing $nursing): self
    {
        $this->nursing = $nursing;

        return $this;
    }

    public function getTreatment(): ?Treatment
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatment $treatment): self
    {
        $this->treatment = $treatment;

        return $this;
    }

    /**
     * @return Collection<int, NursingMedicines>
     */
    public function getNursingMedicines(): Collection
    {
        return $this->nursingMedicines;
    }

    public function addNursingMedicine(NursingMedicines $nursingMedicine): self
    {
        if (!$this->nursingMedicines->contains($nursingMedicine)) {
            $this->nursingMedicines->add($nursingMedicine);
            $nursingMedicine->setNursing($this);
        }

        return $this;
    }

    public function removeNursingMedicine(NursingMedicines $nursingMedicine): self
    {
        if ($this->nursingMedicines->removeElement($nursingMedicine)) {
            // set the owning side to null (unless already changed)
            if ($nursingMedicine->getNursing() === $this) {
                $nursingMedicine->setNursing(null);
            }
        }

        return $this;
    }
}
