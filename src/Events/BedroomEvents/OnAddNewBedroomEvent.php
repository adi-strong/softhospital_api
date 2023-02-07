<?php

namespace App\Events\BedroomEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Bedroom;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewBedroomEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $bedroom = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($bedroom instanceof Bedroom && $method === Request::METHOD_POST) {
      $bedroom->setCreatedAt(new DateTime());
      $bedroom->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
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
