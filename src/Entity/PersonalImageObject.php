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
use App\Controller\CreatePersonalImageObjectAction;
use App\Repository\PersonalImageObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PersonalImageObjectRepository::class)]
#[ApiResource(
  types: ['https://schema.org/PersonalImageObject'],
  operations: [
    new Get(),
    new GetCollection(),
    new Delete(),
    new Post(
      controller: CreatePersonalImageObjectAction::class,
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
      validationContext: ['groups' => ['Default', 'personal_img_obj_create']],
      deserialize: false
    ),
  ],
  normalizationContext: ['groups' => ['personal_img_obj:read']],
  order: ['id' => 'DESC']
)]
class PersonalImageObject
{
  use CreatedAtTrait, UIDTrait;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  #[Groups(['personal_img_obj:read'])]
  private ?int $id = null;

  #[ApiProperty(types: ['https://schema.org/contentUrl'])]
  #[Groups(['personal_img_obj:read'])]
  public ?string $contentUrl = null;

  #[Vich\UploadableField(mapping: 'personal_img_obj', fileNameProperty: 'filePath')]
  #[Assert\NotNull(groups: ['personal_img_obj_create'])]
  public ?File $file = null;

  #[ORM\Column(nullable: true)]
  #[Groups(['personal_img_obj:read'])]
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
