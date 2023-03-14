<?php

namespace App\Events\AppointmentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Appointment;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewAppointmentEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
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

  public function handler(ViewEvent $event)
  {
    $appointment = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($appointment instanceof Appointment && $method === Request::METHOD_POST) {
      $currentUser = $this->user->getUser();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $createdAt = new DateTime();
      $appointmentDate = $appointment->getAppointmentDate() ?? $createdAt;

      $appointment->setUser($currentUser);
      $appointment->setAppointmentDate($appointmentDate);
      $appointment->setCreatedAt($createdAt);
      $appointment->setHospital($hospital);
      $appointment->setFullName($appointment->getPatient()->getFullName());
    }
  }
}
