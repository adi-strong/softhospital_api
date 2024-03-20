<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\AppTraits\CreatedAtTrait;
use App\Controller\StatControllers\LastsActivitiesController;
use App\Repository\ActivitiesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivitiesRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Activities'],
  operations: [
    new Get(
      uriTemplate: '/api/get_lasts_activities_by_month/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: LastsActivitiesController::class,
      name: 'get_lasts_activities_by_month'
    ),

    new Get(
      uriTemplate: '/api/get_lasts_activities_by_last_month/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: LastsActivitiesController::class,
      name: 'get_lasts_activities_by_last_month'
    ),

    new Get(
      uriTemplate: '/api/get_lasts_activities_by_year/{year}/{month}',
      requirements: [ 'year' => '\d+' ],
      controller: LastsActivitiesController::class,
      name: 'get_lasts_activities_by_year'
    ),
  ],
  normalizationContext: ['groups' => ['activity:read']],
  order: ['id' => 'DESC'],
  forceEager: false
)]
class Activities
{
  use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['activity:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[Groups(['activity:read'])]
    private ?User $author = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['activity:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['activity:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['activity:read'])]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[Groups(['activity:read'])]
    private ?Hospital $hospital = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?UserInterface $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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
}
