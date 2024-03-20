<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Repository\ExpenseCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: ExpenseCategoryRepository::class)]
#[ApiResource(
  types: ['https://schema.org/ExpenseCategory'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch(),
    new Delete(),
  ],
  normalizationContext: ['groups' => ['expense_category:read']],
  order: ['id' => 'DESC'],
  forceEager: false,
  paginationEnabled: false,
)]
class ExpenseCategory
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense_category:read', 'expense:read', 'output:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'expenseCategories')]
    private ?Hospital $hospital = null;

    #[ORM\Column(length: 255)]
    #[NotBlank(message: 'Le nom de la ctégorie doit être renseigné.')]
    #[Groups(['expense_category:read', 'expense:read', 'output:read'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    public function __construct()
    {
        $this->boxOutputs = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $boxOutput->setCategory($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getCategory() === $this) {
                $boxOutput->setCategory(null);
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
            $boxExpense->setCategory($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getCategory() === $this) {
                $boxExpense->setCategory(null);
            }
        }

        return $this;
    }
}
