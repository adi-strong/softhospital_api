<?php

namespace App\Events\PatientEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Agent;
use App\Entity\Patient;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewPatientEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $patient = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($patient instanceof Patient && $method === Request::METHOD_POST) {
      $patient->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $patient->setUser($this->user->getUser());
      $patient->setCreatedAt(new DateTime('now'));

      $lastName = $patient?->getLastName();
      $firstName = $patient?->getFirstName();
      $fullName = $patient->getName().' '.$lastName.' '.$firstName;
      $patient->setFullName(trim($fullName, ' '));
    }
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_VALIDATE,
      ]
    ];
  }
}
