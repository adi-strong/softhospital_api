<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait IsDeletedTrait
{
  #[ORM\Column]
  #[Groups([
    'patient:read',
  ])]
  private ?bool $isDeleted = null;

  public function isIsDeleted(): ?bool
  {
    return $this->isDeleted;
  }

  public function setIsDeleted(bool $isDeleted): self
  {
    $this->isDeleted = $isDeleted;

    return $this;
  }
}
