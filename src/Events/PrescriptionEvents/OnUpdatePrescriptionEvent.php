<?php

namespace App\Events\PrescriptionEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Prescription;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdatePrescriptionEvent implements EventSubscriberInterface
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
    $prescription = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($prescription instanceof Prescription && $method === Request::METHOD_PATCH) {
      $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $patient = $prescription->getPatient();
      $prescription->setHospital($hosp);
      $prescription->setUser($this->user->getUser());
      $prescription->setUpdatedAt(new DateTime());
      $prescription->setIsPublished(true);
      $prescription->setFullName($patient?->getFullName());

      $consult = $prescription->getConsultation();
      $consult?->setFullName($consult?->getFullName());
      $consult?->setIsPublished(false);
    }
  }
}
