<?php

namespace App\Events\DepartmentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Department;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateDepartmentEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    $department = $event->getControllerResult();

    if ($department instanceof Department && $method === Request::METHOD_PATCH) {
      $services = $department->getServices();
      if ($department->isIsDeleted() && $services->count() > 0) {
        foreach ($services as $service) {
          $department->removeService($service);
        }
      } // If Services exists

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
