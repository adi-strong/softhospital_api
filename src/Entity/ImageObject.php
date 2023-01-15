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
  use CreatedAtTrait, UIDTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['img_obj:read', 'patient:read'])]
    private ?int $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['img_obj:read', 'patient:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'img_obj', fileNameProperty: 'filePath')]
    #[Assert\NotNull(groups: ['img_obj_create'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['img_obj:read', 'patient:read'])]
    public ?string $filePath = null;

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
}
