<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\UIDTrait;
use App\Controller\CreateImageObjectAction;
use App\Repository\ImageObjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ImageObjectRepository::class)]
#[ApiResource(
  types: ['https://schema.org/ImageObject'],
  operations: [
    new Get(),
    new GetCollection(),
    new Delete(),
    new Post(
      controller: CreateImageObjectAction::class,
      openapiContext: [
        'requestBody' => [
          'content' => [
            'multipart/form-data' => [
              'schema' => [
                'type' => 'object',
                'properties' => [
                  'file' => [
                    'type' => 'string',
                    'format' => 'binary'
                  ]
                ]
              ]
            ]
          ]
        ]
      ],
      validationContext: ['groups' => ['Default', 'img_obj_create']],
      deserialize: false
    ),
  ],
  normalizationContext: ['groups' => ['img_obj:read']],
  order: ['id' => 'DESC']
)]
class ImageObject
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
      'img_obj:read',
      'patient:read',
      'hospital:read',
      'user:read',
      'param:read',
      'covenant:read',
      'consult:read',
      'prescript:read',
      'lab:read',
      'nursing:read',
      'appointment:read',
    ])]
    private ?int $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups([
      'img_obj:read',
      'patient:read',
      'hospital:read',
      'user:read',
      'param:read',
      'covenant:read',
      'consult:read',
      'prescript:read',
      'lab:read',
      'nursing:read',
    ])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'img_obj', fileNameProperty: 'filePath')]
    #[Assert\NotNull(groups: ['img_obj_create'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['img_obj:read', 'patient:read'])]
    public ?string $filePath = null;

    #[ORM\OneToMany(mappedBy: 'logo', targetEntity: Hospital::class)]
    private Collection $hospitals;

    #[ORM\ManyToOne(inversedBy: 'imageObjects')]
    private ?Hospital $hospital = null;

    #[ORM\OneToMany(mappedBy: 'logo', targetEntity: Covenant::class)]
    private Collection $covenants;

    public function __construct()
    {
        $this->hospitals = new ArrayCollection();
        $this->covenants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

  #[Assert\Callback]
  public function validate(ExecutionContextInterface $context, $payload): void
  {
    $types = ['image/jpg', 'image/jpeg', 'image/png'];
    if (!in_array($this->file->getMimeType(), $types)) {
      $context
        ->buildViolation("Extension non valide: 'jpg' | 'jpeg' | 'png'")
        ->atPath('file')
        ->addViolation()
      ;
    }
  }

  /**
   * @return Collection<int, Hospital>
   */
  public function getHospitals(): Collection
  {
      return $this->hospitals;
  }

  public function addHospital(Hospital $hospital): self
  {
      if (!$this->hospitals->contains($hospital)) {
          $this->hospitals->add($hospital);
          $hospital->setLogo($this);
      }

      return $this;
  }

  public function removeHospital(Hospital $hospital): self
  {
      if ($this->hospitals->removeElement($hospital)) {
          // set the owning side to null (unless already changed)
          if ($hospital->getLogo() === $this) {
              $hospital->setLogo(null);
          }
      }

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
          $covenant->setLogo($this);
      }

      return $this;
  }

  public function removeCovenant(Covenant $covenant): self
  {
      if ($this->covenants->removeElement($covenant)) {
          // set the owning side to null (unless already changed)
          if ($covenant->getLogo() === $this) {
              $covenant->setLogo(null);
          }
      }

      return $this;
  }
}
