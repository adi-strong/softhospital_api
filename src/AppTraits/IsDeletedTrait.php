<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;

trait IsDeletedTrait
{
  #[ORM\Column]
  private ?bool $isDeleted = false;

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
