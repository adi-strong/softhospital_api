<?php

namespace App\Events\InvoiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\CovenantInvoice;
use App\Repository\BoxRepository;
use App\Services\BoxSumService;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterUpdateCovenantEvent implements EventSubscriberInterface
{
  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::POST_WRITE,
      ]
    ];
  }

  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly BoxRepository $boxRepository,
    private readonly BoxSumService $boxSumService,
    private readonly HandleCurrentUserService $user) { }

  public function handler(ViewEvent $event)
  {
    $invoice = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($invoice instanceof CovenantInvoice && $method === Request::METHOD_PATCH) {
      $hosp = $this->user->getHospitalCenter() ?? $this->user->getHospital();
      $box = $this->boxRepository->findBox($hosp->getId());
      $box?->setSum($this->boxSumService->getBoxSum($box));
      $this->em->flush();
    }
  }
}
