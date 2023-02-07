<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\Controller\CreatePdfObjectAction;
use App\Repository\PdfObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PdfObjectRepository::class)]
#[ApiResource(
  types: ['https://schema.org/PdfObject'],
  operations: [
    new Get(),
    new GetCollection(),
    new Delete(),
    new Post(
      controller: CreatePdfObjectAction::class,
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
      validationContext: ['groups' => ['Default', 'pdf_obj_create']],
      deserialize: false
    ),
  ],
  normalizationContext: ['groups' => ['pdf_obj:read']],
  order: ['id' => 'DESC']
)]
class PdfObject
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['pdf_obj:read', 'covenant:read', 'patient:read'])]
    private ?int $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['pdf_obj:read', 'covenant:read', 'patient:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'pdf_obj', fileNameProperty: 'filePath')]
    #[Assert\NotNull(groups: ['pdf_obj_create'])]
    #[Groups(['pdf_obj:read', 'covenant:read', 'patient:read'])]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['pdf_obj:read', 'covenant:read', 'patient:read'])]
    public ?string $filePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
