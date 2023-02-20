<?php

namespace App\Events\ProviderEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Provider;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddProviderEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $medicine = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($medicine instanceof Provider && $method === Request::METHOD_POST) {
      $medicine->setCreatedAt(new DateTime());
      $medicine->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $medicine->setUser($this->user->getUser());
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
