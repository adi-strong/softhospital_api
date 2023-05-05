<?php

namespace App\Events\ResetPasswordEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ResetPassNotifier;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnDeleteResetNotifierDataSubscriber implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'onCheckExpireNotifiers',
        EventPriorities::PRE_RESPOND,
      ]
    ];
  }

  public function onCheckExpireNotifiers(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    if ($method === Request::METHOD_GET) {
      $end = new DateTime();
      $notifiers = $this->em->getRepository(ResetPassNotifier::class)->findAll();
      foreach ($notifiers as $notifier) {
        $diff = $notifier->getReleasedAt()->diff($end)->i;
        if ($diff >= 15) $this->em->remove($notifier);
      }

      $this->em->flush();
    }
  }
}
