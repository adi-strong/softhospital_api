<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

trait CreatedAtTrait
{
  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  #[Groups([
    'user:read',
    'patient:read',
    'covenant:read',
    'hospital:read',
    'img_obj:read',
    'personal_img_obj:read',
    'param:read',
    'box_historic:read',
    'box:read',
    'service:read',
    'department:read',
    'office:read',
    'agent:read',
    'expense:read',
    'expense_category:read',
    'output:read',
    'input:read',
  ])]
  protected ?\DateTimeInterface $createdAt = null;

  public function getCreatedAt(): ?\DateTimeInterface
  {
    return $this->createdAt;
  }

  public function setCreatedAt(?\DateTimeInterface $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }
}
