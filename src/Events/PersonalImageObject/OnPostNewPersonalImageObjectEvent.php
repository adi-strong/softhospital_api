<?php

namespace App\Events\PersonalImageObject;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\PersonalImageObject;
use App\Services\HandleCurrentUserService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnPostNewPersonalImageObjectEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $currentUser)
  {
  }

  public function handler(ViewEvent $event)
  {
    $img = $event->getControllerResult();
    $request = $event->getRequest()->getMethod();
    if ($img instanceof PersonalImageObject && $request === Request::METHOD_POST) {
      if ($this->currentUser->getUser() !== null) {
        $img->setUId($this->currentUser->getUId());
      }
      $img->setCreatedAt(new \DateTime('now'));
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
