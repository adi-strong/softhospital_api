<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\OfficeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfficeRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Office'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
    new Patch()
  ],
  normalizationContext: ['groups' => ['office:read']],
  order: ['id' => 'DESC'],
  paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'ipartial'])]
class Office
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['office:read', 'agent:read', 'consult:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La fonction doit être renseigné.')]
    #[Assert\Length(
      min: 2,
      max: 255,
      minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.',
      maxMessage: 'Ce champs doit faire {{ limit }} caractères maximum.')]
    #[Groups(['office:read', 'agent:read', 'user:read', 'consult:read'])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'offices')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'office', targetEntity: Agent::class)]
    private Collection $agents;

    public function __construct()
    {
        $this->agents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->setOffice($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getOffice() === $this) {
                $agent->setOffice(null);
            }
        }

        return $this;
    }
}
