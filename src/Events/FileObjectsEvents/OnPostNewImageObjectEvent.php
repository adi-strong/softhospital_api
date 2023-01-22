<?php

namespace App\Events\FileObjectsEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ImageObject;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnPostNewImageObjectEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $img = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($img instanceof ImageObject && $method === Request::METHOD_POST) {
      if ($this->user->getUser() !== null &&
        ($this->user->getHospital() !== null || $this->user->getHospitalCenter() !== null)) {
        $img->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      }
      $img->setCreatedAt(new DateTime('now'));
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
