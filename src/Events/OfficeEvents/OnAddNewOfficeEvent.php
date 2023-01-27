<?php

namespace App\Events\OfficeEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Office;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewOfficeEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $office = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($office instanceof Office && $method === Request::METHOD_POST) {
      $office->setCreatedAt(new DateTime());
      $office->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
    }
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_WRITE
      ]
    ];
  }
}
