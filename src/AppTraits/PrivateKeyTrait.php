<?php

namespace App\AppTraits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait PrivateKeyTrait
{
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $privateKey = null;

  #[ORM\Column]
  #[Groups(['patient:read', 'covenant:read'])]
  private ?bool $isPrivateKeyExists = false;

  public function getPrivateKey(): ?string
  {
    return $this->privateKey;
  }

  public function setPrivateKey(?string $privateKey): self
  {
    $this->privateKey = $privateKey;

    return $this;
  }

  public function isIsPrivateKeyExists(): ?bool
  {
    return $this->isPrivateKeyExists;
  }

  public function setIsPrivateKeyExists(bool $isPrivateKeyExists): self
  {
    $this->isPrivateKeyExists = $isPrivateKeyExists;

    return $this;
  }
}
