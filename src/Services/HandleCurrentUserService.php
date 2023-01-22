<?php

namespace App\Services;

use App\Entity\Hospital;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class HandleCurrentUserService
{
  public function __construct(private readonly Security $security, private readonly AuthorizationCheckerInterface $checker) { }

  public function getUser(): ?UserInterface
  {
    return $this->security->getUser();
  }

  public function getUId(): ?string
  {
    return $this->getUser()->getUId();
  }

  public function getHospital(): ?Hospital
  {
    return $this->getUser()->getHospital();
  }

  public function getHospitalCenter(): ?Hospital
  {
    return $this->getUser()->getHospitalCenter();
  }

  public function getAuth(): ?AuthorizationCheckerInterface
  {
    return $this->checker;
  }
}
