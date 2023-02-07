<?php

namespace App\Events\TreatmentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Treatment;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewTreatmentEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $treatment = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($treatment instanceof Treatment && $method === Request::METHOD_POST) {
      $treatment->setCreatedAt(new DateTime());
      $treatment->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
    }
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_WRITE,
      ]
    ];
  }
}