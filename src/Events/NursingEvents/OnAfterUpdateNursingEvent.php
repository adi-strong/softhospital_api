<?php

namespace App\Events\NursingEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Nursing;
use App\Repository\BoxRepository;
use App\Services\BoxSumService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterUpdateNursingEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly BoxRepository $boxRepository,
    private readonly BoxSumService $boxSumService)
  {
  }

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

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    $nursing = $event->getControllerResult();

    if ($nursing instanceof Nursing && $method === Request::METHOD_PATCH) {
      $hospital = $nursing->getHospital();
      $box = $this->boxRepository->findBox($hospital->getId());
      $box?->setSum($this->boxSumService->getBoxSum($box));
      $this->em->flush();
    }
  }
}
