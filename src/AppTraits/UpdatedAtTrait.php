<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Serializer\Annotation\Groups;

trait UpdatedAtTrait
{

  #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
  #[Groups([
    'param:read',
  ])]
  private ?\DateTimeInterface $updatedAt = null;

  public function getUpdatedAt(): ?\DateTimeInterface
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }
}
