<?php

namespace App\Events\NursingEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Nursing;
use App\Repository\NursingTreatmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateNursingEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly NursingTreatmentRepository $repository, private readonly EntityManagerInterface $em) { }

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
    $method = $event->getRequest()->getMethod();
    $nursing = $event->getControllerResult();
    if ($nursing instanceof Nursing && $method === Request::METHOD_PATCH) {
      $nursing->setIsPublished(true);
      $consult = $nursing->getConsultation();
      $treatments = $nursing->treatmentValues;

      $patient = $nursing->getPatient();
      $nursing->setFullName($patient?->getFullName());
      $consult?->setFullName($patient?->getFullName());
      $nursing->setConsultation($consult);

      if (null !== $treatments) {
        foreach ($treatments as $treatment) {
          $id = $treatment['id'] ?? 0;
          $medicines = $treatment['medicines'] ?? [];
          $item = $this->repository->findNursingTreatment2($id, $nursing);
          $item?->setMedicines($medicines);
        }
      }

      $this->em->flush();
    }
  }
}
