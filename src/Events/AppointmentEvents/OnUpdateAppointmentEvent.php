<?php

namespace App\Events\AppointmentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Appointment;
use App\Entity\Consultation;
use App\Entity\Invoice;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateAppointmentEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em)
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
    if ($appointment instanceof Appointment && $method === Request::METHOD_PATCH) {
      $currentUser = $this->user->getUser();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $createdAt = new DateTime();
      $appointmentDate = $appointment->getAppointmentDate() ?? $createdAt;
      $isConsultation = $appointment->isConsultation;
      $doctor = $appointment->getDoctor();
      $patient = $appointment->getPatient();
      $fullName = $patient?->getFullName();

      $appointment->setAppointmentDate($appointmentDate);
      $appointment->setFullName($fullName);

      if ($isConsultation === true) {
        $consultation = (new Consultation())
          ->setUser($currentUser)
          ->setHospital($hospital)
          ->setPatient($patient)
          ->setFullName($fullName)
          ->setCreatedAt($createdAt)
          ->setDoctor($doctor);

        $invoice = (new Invoice())
          ->setPatient($patient)
          ->setConsultation($consultation)
          ->setAmount(0)
          ->setFullName($fullName)
          ->setTotalAmount(0)
          ->setUser($currentUser)
          ->setHospital($hospital);

        $appointment->setConsultation($consultation);
        $this->em->persist($invoice);
      }

      $this->em->flush();
    }
  }
}
