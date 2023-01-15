<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\AppTraits\CreatedAtTrait;
use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Hospital'],
  normalizationContext: ['groups' => ['hospital:read']],
  order: ['id' => 'DESC'],
)]
#[UniqueEntity('email', message: 'Cette adresse email existe déjà.')]
class Hospital
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital:read', 'user:read'])]
    #[Assert\NotBlank(message: 'La dénomination doit être renseigné.')]
    #[Assert\Length(min: 2, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    private ?string $denomination = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['hospital:read', 'user:read'])]
    private ?string $unitName = null;

    #[ORM\OneToOne(inversedBy: 'hospital', cascade: ['persist', 'remove'])]
    #[Groups(['hospital:read'])]
    #[Assert\NotBlank(message: 'L\'utilisateur doit être renseigné.')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'hospitalCenter', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true, unique: true)]
    #[Assert\Email(message: 'Adresse invalide.')]
    private ?string $email = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
}
