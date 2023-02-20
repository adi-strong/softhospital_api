<?php

namespace App\Events\MedicineEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\MedicineInvoice;
use App\Repository\BoxRepository;
use App\Services\BoxSumService;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterPostMedicineSaleEvent implements EventSubscriberInterface
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
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em,
    private readonly BoxRepository $boxRepository,
    private readonly BoxSumService $boxSumService)
  {
  }

  public function handler(ViewEvent $event)
  {
    $invoice = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($invoice instanceof MedicineInvoice && $method === Request::METHOD_POST) {
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();

      if ($this->user->getUser() !== null) {
        $findBox = $this->boxRepository->findBox($hospital->getId());
        $findBox?->setSum($this->boxSumService->getBoxSum($findBox));
        $this->em->flush();
      }
    }
  }
}
