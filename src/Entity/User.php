<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\AppTraits\UIDTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
  types: ['https://schema.org/User'],
  operations: [
    new Get(),
    new Post(),
    new GetCollection(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['user:read']],
  order: ['id' => 'DESC']
)]
#[UniqueEntity('username', message: 'Ce username est déjà pris.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  use CreatedAtTrait, UIDTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'patient:read', 'hospital:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'patient:read', 'hospital:read'])]
    #[Assert\NotBlank(message: 'Le username doit être renseigné.')]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string
     * The hashed password
     */
    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank(message: 'Le mot de passe doit être renseigné.')]
    #[Assert\Length(min: 4, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.')]
    private string $password;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    #[Groups(['user:read', 'patient:read'])]
    private ?self $user = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: self::class)]
    private Collection $users;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read', 'hospital:read'])]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read'])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?Hospital $hospitalCenter = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone doit être renseigné')]
    #[Assert\Length(min: 9, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    #[Assert\Regex('#^([+]\d{2}[-. ])?\d{9,14}$#', message: 'Numéro de téléphone invalide.')]
    private ?string $tel = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxInput::class)]
    private Collection $boxInputs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?PersonalImageObject $profile = null;

    public ?bool $isChangingPassword = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->boxInputs = new ArrayCollection();
        $this->boxOutputs = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUser(): ?self
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUser($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUser() === $this) {
                $user->setUser(null);
            }
        }

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
        // unset the owning side of the relation if necessary
        if ($hospital === null && $this->hospital !== null) {
            $this->hospital->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($hospital !== null && $hospital->getUser() !== $this) {
            $hospital->setUser($this);
        }

        $this->hospital = $hospital;

        return $this;
    }

    public function getHospitalCenter(): ?Hospital
    {
        return $this->hospitalCenter;
    }

    public function setHospitalCenter(?Hospital $hospitalCenter): self
    {
        $this->hospitalCenter = $hospitalCenter;

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

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
            $boxInput->setUser($this);
        }

        return $this;
    }

    public function removeBoxInput(BoxInput $boxInput): self
    {
        if ($this->boxInputs->removeElement($boxInput)) {
            // set the owning side to null (unless already changed)
            if ($boxInput->getUser() === $this) {
                $boxInput->setUser(null);
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
            $boxOutput->setUser($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getUser() === $this) {
                $boxOutput->setUser(null);
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
            $boxExpense->setUser($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getUser() === $this) {
                $boxExpense->setUser(null);
            }
        }

        return $this;
    }

    public function getProfile(): ?PersonalImageObject
    {
        return $this->profile;
    }

    public function setProfile(?PersonalImageObject $profile): self
    {
        $this->profile = $profile;

        return $this;
    }
}
