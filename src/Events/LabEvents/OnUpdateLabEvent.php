<?php

namespace App\Events\LabEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Activities;
use App\Entity\Lab;
use App\Entity\Prescription;
use App\Repository\ExamRepository;
use App\Repository\LabResultRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Constraints\Date;

class OnUpdateLabEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly LabResultRepository $labResultRepository,
    private readonly ExamRepository $examRepository,
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
    $lab = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($lab instanceof Lab && $method === Request::METHOD_PATCH) {
      $values = $lab->values;
      $consultation = $lab->getConsultation();
      $prescription = $lab->getPrescription();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();

      $patient = $lab->getPatient();
      $lab->setFullName($patient?->getFullName());
      $consultation?->setFullName($patient?->getFullName());
      $consultation?->setIsPublished(false);

      if ($lab->isIsPublished() === false) {
        foreach ($values as $value) {
          $examId = $value['id'];
          $findExam = $this->examRepository->find($examId);
          if (null !== $findExam) {
            $findLabExam = $this->labResultRepository->findLabExam($findExam, $lab);
            if (null !== $findLabExam) {
              $lab->setIsPublished(true);
              $lab->setUserPublisher($this->user->getUser());
              $lab->setUpdatedAt(new DateTime());
              $consultation?->setIsPublished(false);
            }
          }
        }

        // Init prescription
        if (null === $prescription) {
          $initPrescription = (new Prescription())
            ->setLab($lab)
            ->setPatient($lab->getPatient())
            ->setConsultation($lab?->getConsultation())
            ->setIsPublished(false)
            ->setFullName($patient?->getFullName())
            ->setHospital($hospital);

          $this->em->persist($initPrescription);
        }
        // End Init prescription
      }
      else $lab->setDescriptions(null);

      $activity = (new Activities())
        ->setTitle('Publication des résultats d\'examens')
        ->setCreatedAt(new DateTime())
        ->setHospital($hospital)
        ->setAuthor($this->user->getUser())
        ->setDescription("Publication des résultats des examens du patient : ".$patient->getFullName());
      $this->em->persist($activity);

      $this->em->flush();
    }
  }
}
