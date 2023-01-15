<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
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
  normalizationContext: ['groups' => ['user:read']],
  order: ['id' => 'DESC'],
)]
#[UniqueEntity('email', message: 'Cette adresse email existe déjà.')]
#[UniqueEntity('username', message: 'Ce username est déjà pris.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  use CreatedAtTrait, UIDTrait;

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

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['user:read', 'hospital:read'])]
    #[Assert\NotBlank(message: 'L\'adresse email doit être renseigné.')]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read'])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?Hospital $hospitalCenter = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function setEmail(string $email): self
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
}
