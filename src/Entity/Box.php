<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\BoxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: BoxRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Box'],
  operations: [
    new GetCollection(),
  ],
  normalizationContext: ['groups' => ['box:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
class Box
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['box:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'boxes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['box:read'])]
    #[NotBlank(message: 'L\'hôpital doit être renseigné.')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'box', targetEntity: BoxHistoric::class)]
    private Collection $boxHistorics;

    #[ORM\OneToMany(mappedBy: 'box', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    #[ORM\OneToMany(mappedBy: 'box', targetEntity: BoxInput::class)]
    private Collection $boxInputs;

    #[ORM\OneToMany(mappedBy: 'box', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['box:read'])]
    private ?string $sum = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['box:read'])]
    private ?string $outputSum = null;

    public function __construct()
    {
        $this->boxHistorics = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
        $this->boxInputs = new ArrayCollection();
        $this->boxOutputs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, BoxHistoric>
     */
    public function getBoxHistorics(): Collection
    {
        return $this->boxHistorics;
    }

    public function addBoxHistoric(BoxHistoric $boxHistoric): self
    {
        if (!$this->boxHistorics->contains($boxHistoric)) {
            $this->boxHistorics->add($boxHistoric);
            $boxHistoric->setBox($this);
        }

        return $this;
    }

    public function removeBoxHistoric(BoxHistoric $boxHistoric): self
    {
        if ($this->boxHistorics->removeElement($boxHistoric)) {
            // set the owning side to null (unless already changed)
            if ($boxHistoric->getBox() === $this) {
                $boxHistoric->setBox(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxExpense>
     */
    public function getBoxExpenses(): Collection
    {
        return $this->boxExpenses;
    }

    public function addBoxExpense(BoxExpense $boxExpense): self
    {
        if (!$this->boxExpenses->contains($boxExpense)) {
            $this->boxExpenses->add($boxExpense);
            $boxExpense->setBox($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getBox() === $this) {
                $boxExpense->setBox(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxInput>
     */
    public function getBoxInputs(): Collection
    {
        return $this->boxInputs;
    }

    public function addBoxInput(BoxInput $boxInput): self
    {
        if (!$this->boxInputs->contains($boxInput)) {
            $this->boxInputs->add($boxInput);
            $boxInput->setBox($this);
        }

        return $this;
    }

    public function removeBoxInput(BoxInput $boxInput): self
    {
        if ($this->boxInputs->removeElement($boxInput)) {
            // set the owning side to null (unless already changed)
            if ($boxInput->getBox() === $this) {
                $boxInput->setBox(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxOutput>
     */
    public function getBoxOutputs(): Collection
    {
        return $this->boxOutputs;
    }

    public function addBoxOutput(BoxOutput $boxOutput): self
    {
        if (!$this->boxOutputs->contains($boxOutput)) {
            $this->boxOutputs->add($boxOutput);
            $boxOutput->setBox($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getBox() === $this) {
                $boxOutput->setBox(null);
            }
        }

        return $this;
    }

    public function getSum(): ?string
    {
        return $this->sum;
    }

    public function setSum(?string $sum): self
    {
        $this->sum = $sum;

        return $this;
    }

    public function getOutputSum(): ?string
    {
        return $this->outputSum;
    }

    public function setOutputSum(?string $outputSum): self
    {
        $this->outputSum = $outputSum;

        return $this;
    }
}
