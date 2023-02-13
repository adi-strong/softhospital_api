<?php

namespace App\Events\CovenantEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Covenant;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewCovenantEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $covenant = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($covenant instanceof Covenant && $method === Request::METHOD_POST) {
      $covenant->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $covenant->setCreatedAt(new DateTime());
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