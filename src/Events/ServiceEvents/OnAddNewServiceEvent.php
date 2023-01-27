<?php

namespace App\Events\ServiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Service;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewServiceEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $service = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($service instanceof Service && $method === Request::METHOD_POST) {
      $service->setCreatedAt(new DateTime());
      $service->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
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
