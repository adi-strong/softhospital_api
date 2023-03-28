<?php

namespace App\Events\PatientEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;
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

  public function __construct(private readonly EntityManagerInterface $em) { }

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

      $consultations = $patient->getConsultations();
      $nursingItems = $patient->getNursings();
      $prescriptions = $patient->getPrescriptions();
      $labs = $patient->getLabs();

      if ($consultations->count() > 0) {
        foreach ($consultations as $consultation) $consultation->setFullName($fullName);
      } // handle edit consultations full_name

      if ($nursingItems->count() > 0) {
        foreach ($nursingItems as $nursing) $nursing->setFullName($fullName);
      } // handle edit nursing full_name

      if ($prescriptions->count() > 0) {
        foreach ($prescriptions as $prescription) $prescription->setFullName($fullName);
      } // handle edit prescriptions full_name

      if ($labs->count() > 0) {
        foreach ($labs as $lab) $lab->setFullName($fullName);
      } // handle edit labs full_name

      $this->em->flush();
    }
  }
}
