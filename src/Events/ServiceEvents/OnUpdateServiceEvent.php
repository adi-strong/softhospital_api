<?php

namespace App\Events\ServiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateServiceEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $service = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($service instanceof Service && $method === Request::METHOD_POST) {
      $agents = $service->getAgents();
      if ($service->isIsDeleted() && $agents->count() > 0) {
        foreach ($agents as $agent) {
          $service->removeAgent($agent);
        }
      }

      $this->em->flush();
    }
  }

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
