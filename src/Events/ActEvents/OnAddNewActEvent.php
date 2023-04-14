<?php

namespace App\Events\ActEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Act;
use App\Entity\Treatment;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewActEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user) { }

  public function handler(ViewEvent $event)
  {
    $act = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($act instanceof Act && $method === Request::METHOD_POST) {
      $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $act->setCreatedAt(new DateTime());
      $act->setHospital($hosp);
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
