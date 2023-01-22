<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Hospital'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['hospital:read']],
  order: ['id' => 'DESC'],
)]
class Hospital
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    #[Assert\NotBlank(message: 'La dénomination doit être renseigné.')]
    #[Assert\Length(min: 2, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    private ?string $denomination = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Ce champs doit contenir {{ limit }} caractères au maximum .')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $unitName = null;

    #[ORM\OneToOne(inversedBy: 'hospital', cascade: ['persist', 'remove'])]
    #[Groups(['hospital:read'])]
    #[Assert\NotBlank(message: 'L\'utilisateur doit être renseigné.')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'hospitalCenter', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(min: 9, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    #[Assert\Regex('#^([+]\d{2}[-. ])?\d{9,14}$#', message: 'Numéro de téléphone invalide.')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Adresse invalide.')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?ImageObject $logo = null;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Patient::class)]
    private Collection $patients;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Parameters::class)]
    private Collection $parameters;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Covenant::class)]
    private Collection $covenants;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ImageObject::class)]
    private Collection $imageObjects;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxInput::class)]
    private Collection $boxInputs;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ExpenseCategory::class)]
    private Collection $expenseCategories;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->covenants = new ArrayCollection();
        $this->imageObjects = new ArrayCollection();
        $this->boxInputs = new ArrayCollection();
        $this->boxOutputs = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
        $this->expenseCategories = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setHospitalCenter($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getHospitalCenter() === $this) {
                $user->setHospitalCenter(null);
            }
        }

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
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

    public function getLogo(): ?ImageObject
    {
        return $this->logo;
    }

    public function setLogo(?ImageObject $logo): self
    {
        $this->logo = $logo;

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
            $patient->setHospital($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getHospital() === $this) {
                $patient->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parameters>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameters $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters->add($parameter);
            $parameter->setHospital($this);
        }

        return $this;
    }

    public function removeParameter(Parameters $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getHospital() === $this) {
                $parameter->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Covenant>
     */
    public function getCovenants(): Collection
    {
        return $this->covenants;
    }

    public function addCovenant(Covenant $covenant): self
    {
        if (!$this->covenants->contains($covenant)) {
            $this->covenants->add($covenant);
            $covenant->setHospital($this);
        }

        return $this;
    }

    public function removeCovenant(Covenant $covenant): self
    {
        if ($this->covenants->removeElement($covenant)) {
            // set the owning side to null (unless already changed)
            if ($covenant->getHospital() === $this) {
                $covenant->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImageObject>
     */
    public function getImageObjects(): Collection
    {
        return $this->imageObjects;
    }

    public function addImageObject(ImageObject $imageObject): self
    {
        if (!$this->imageObjects->contains($imageObject)) {
            $this->imageObjects->add($imageObject);
            $imageObject->setHospital($this);
        }

        return $this;
    }

    public function removeImageObject(ImageObject $imageObject): self
    {
        if ($this->imageObjects->removeElement($imageObject)) {
            // set the owning side to null (unless already changed)
            if ($imageObject->getHospital() === $this) {
                $imageObject->setHospital(null);
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
            $boxInput->setHospital($this);
        }

        return $this;
    }

    public function removeBoxInput(BoxInput $boxInput): self
    {
        if ($this->boxInputs->removeElement($boxInput)) {
            // set the owning side to null (unless already changed)
            if ($boxInput->getHospital() === $this) {
                $boxInput->setHospital(null);
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
            $boxOutput->setHospital($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getHospital() === $this) {
                $boxOutput->setHospital(null);
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
            $boxExpense->setHospital($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getHospital() === $this) {
                $boxExpense->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExpenseCategory>
     */
    public function getExpenseCategories(): Collection
    {
        return $this->expenseCategories;
    }

    public function addExpenseCategory(ExpenseCategory $expenseCategory): self
    {
        if (!$this->expenseCategories->contains($expenseCategory)) {
            $this->expenseCategories->add($expenseCategory);
            $expenseCategory->setHospital($this);
        }

        return $this;
    }

    public function removeExpenseCategory(ExpenseCategory $expenseCategory): self
    {
        if ($this->expenseCategories->removeElement($expenseCategory)) {
            // set the owning side to null (unless already changed)
            if ($expenseCategory->getHospital() === $this) {
                $expenseCategory->setHospital(null);
            }
        }

        return $this;
    }
}
