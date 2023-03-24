<?php

namespace App\AppTraits;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

trait FullNameTrait
{
  #[ORM\Column(length: 255, nullable: true)]
  #[Groups([
    'prescript:read',
    'lab:read',
  ])]
  private ?string $fullName = null;

  public function getFullName(): ?string
  {
    return $this->fullName;
  }

  public function setFullName(?string $fullName): self
  {
    $this->fullName = $fullName;

    return $this;
  }
}
