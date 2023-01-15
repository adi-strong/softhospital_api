<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;

trait UIDTrait
{
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $uId = null;

  public function getUId(): ?string
  {
    return $this->uId;
  }

  public function setUId(?string $uId): self
  {
    $this->uId = $uId;

    return $this;
  }
}
