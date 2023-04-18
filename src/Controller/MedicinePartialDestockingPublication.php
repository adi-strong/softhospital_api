<?php

namespace App\Controller;

use App\Controller\handler\MedicinePartialDestockingHandler;
use App\Entity\Medicine;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class MedicinePartialDestockingPublication extends AbstractController
{
  public function __construct(
    private readonly RequestStack $requestStack,
    private readonly MedicinePartialDestockingHandler $handler,
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em)
  {
  }

  public function __invoke(Medicine $medicine): Medicine
  {
    $this->handler->handle($medicine, $this->requestStack, $this->em, $this->user->getUser());
    $this->em->flush();
    return $medicine;
  }
}
