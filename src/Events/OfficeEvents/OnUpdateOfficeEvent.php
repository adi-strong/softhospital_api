<?php

namespace App\Events\OfficeEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Office;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateOfficeEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $office = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($office instanceof Office && $method === Request::METHOD_PATCH) {
      $agents = $office->getAgents();
      if ($office->isIsDeleted() && $agents->count() > 0) {
        foreach ($agents as $agent) {
          $office->removeAgent($agent);
        }
      }

      $this->em->flush();
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
