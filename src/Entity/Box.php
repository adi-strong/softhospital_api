<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
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
    new Get(),
    new Post(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['box:read']],
  order: ['id' => 'DESC'],
)]
class Box
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['box:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['box:read'])]
    private ?string $sum = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['box:read'])]
    #[NotBlank(message: 'L\'hôpital doit être renseigné.')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'box', targetEntity: BoxHistoric::class)]
    private Collection $boxHistorics;

    public function __construct()
    {
        $this->boxHistorics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
