<?php

namespace App\Controller;

use App\Controller\handler\ResetPassHandler;
use App\Entity\ResetPassNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class ResetPassPublication extends AbstractController
{
  public function __construct(
    private readonly ResetPassHandler $handler,
    private readonly RequestStack $requestStack,
    private readonly EntityManagerInterface $em)
  {
  }

  /**
   * @throws TransportExceptionInterface
   */
  #[Route('/reset_pass_notifier', methods: ['POST'])]
  public function notifyResetPassword(): JsonResponse
  {
    $notifier = new ResetPassNotifier();
    $this->handler->handle($notifier, $this->requestStack, $this->em);

    return new JsonResponse($notifier);
  }
}
