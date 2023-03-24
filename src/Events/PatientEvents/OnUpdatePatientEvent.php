<?php

namespace App\Events\PatientEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Patient;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdatePatientEvent implements EventSubscriberInterface
{
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

  public function handler(ViewEvent $event)
  {
    $patient = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($patient instanceof Patient && $method === Request::METHOD_PATCH) {
      // Patient fullName
      $lastName = $patient->getLastName();
      $firstName = $patient->getFirstName();
      $fullName = $patient->getName().' '.$lastName.' '.$firstName;
      $patient->setFullName(trim($fullName, ' '));
      // End Patient fullName

      // ...
    }
  }
}
