<?php

namespace App\Events\UserEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OnUpdateUserEvent implements EventSubscriberInterface
{
  public function __construct(private readonly ?UserPasswordHasherInterface $encoder)
  {
  }

  public function handler(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($user instanceof User && $method === Request::METHOD_PATCH) {
      if ($user->isChangingPassword) {
        $newPassword = $this->encoder->hashPassword($user, $user->getPassword());
        $user->setPassword($newPassword);
      }
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
