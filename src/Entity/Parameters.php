<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\UpdatedAtTrait;
use App\Repository\ParametersRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParametersRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Parameters'],
  operations: [
    new GetCollection(),
    new Get(),
    new Post(),
  ],
  normalizationContext: ['groups' => ['param:read']],
  order: ['id' => 'DESC'],
)]
class Parameters
{
  use CreatedAtTrait, UpdatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['param:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['param:read'])]
    #[Assert\NotBlank(message: 'La devise doit être renseignée.')]
    private ?string $currency = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\Regex('#^\d*#', message: "Nombre ou montant invalide.")]
    #[Groups(['param:read'])]
    private ?string $rate = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['param:read'])]
    private ?string $secondCurrency = null;

    #[ORM\Column(length: 255)]
    #[Groups(['param:read'])]
    #[Assert\NotBlank(message: 'Ce champs doit être renseigné.')]
    private ?string $name = null;

    #[ORM\Column(length: 5)]
    #[Groups(['param:read'])]
    #[Assert\NotBlank(message: 'Ce champs doit être renseigné.')]
    #[Assert\Length(min: 2, max: 5)]
    private ?string $code = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Groups(['param:read'])]
    #[Assert\Length(min: 2, max: 5)]
    private ?string $secondCode = null;

    #[ORM\ManyToOne(inversedBy: 'parameters')]
    #[Groups(['param:read'])]
    private ?Hospital $hospital = null;

    public ?bool $isUpdated = false;

    #[ORM\Column(length: 2, nullable: true, columnDefinition: 'char(1) default null')]
    #[Assert\Choice(['+', '-', '*', '/'], message: 'Opération arithmétique non supportée.')]
    #[Groups(['param:read'])]
    private ?string $fOperation = null;

    #[ORM\Column(length: 2, nullable: true, columnDefinition: 'char(1) default null')]
    #[Assert\Choice(['+', '-', '*', '/'], message: 'Opération arithmétique non supportée.')]
    #[Groups(['param:read'])]
    private ?string $lOperation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(?string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getSecondCurrency(): ?string
    {
        return $this->secondCurrency;
    }

    public function setSecondCurrency(?string $secondCurrency): self
    {
        $this->secondCurrency = $secondCurrency;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSecondCode(): ?string
    {
        return $this->secondCode;
    }

    public function setSecondCode(?string $secondCode): self
    {
        $this->secondCode = $secondCode;

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

    public function getFOperation(): ?string
    {
        return $this->fOperation;
    }

    public function setFOperation(?string $fOperation): self
    {
        $this->fOperation = $fOperation;

        return $this;
    }

    public function getLOperation(): ?string
    {
        return $this->lOperation;
    }

    public function setLOperation(?string $lOperation): self
    {
        $this->lOperation = $lOperation;

        return $this;
    }
}
