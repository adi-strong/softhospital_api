<?php

namespace App\Events\ConsultationEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ActsInvoiceBasket;
use App\Entity\Consultation;
use App\Entity\ExamsInvoiceBasket;
use App\Entity\Hospitalization;
use App\Entity\Lab;
use App\Entity\LabResult;
use App\Entity\Nursing;
use App\Entity\NursingTreatment;
use App\Entity\TreatmentInvoiceBasket;
use App\Repository\ActRepository;
use App\Repository\ActsInvoiceBasketRepository;
use App\Repository\ExamRepository;
use App\Repository\ExamsInvoiceBasketRepository;
use App\Repository\LabRepository;
use App\Repository\NursingRepository;
use App\Repository\TreatmentInvoiceBasketRepository;
use App\Repository\TreatmentRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateConsultationEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em,
    private readonly ExamRepository $examRepository,
    private readonly ActRepository $actRepository,
    private readonly TreatmentRepository $treatmentRepository,
    private readonly ExamsInvoiceBasketRepository $examsInvoiceBasketRepository,
    private readonly ActsInvoiceBasketRepository $actsInvoiceBasketRepository,
    private readonly TreatmentInvoiceBasketRepository $treatmentInvoiceBasketRepository,
    private readonly LabRepository $labRepository,
    private readonly NursingRepository $nursingRepository)
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

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event)
  {
    $consultation = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($consultation instanceof Consultation && $method === Request::METHOD_PATCH) {
      $createdAt = $consultation->getCreatedAt() ?? new DateTime();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $note = $consultation->getNote() ?? null;
      $patient = $consultation->getPatient();
      $currentUser = $consultation->getUser() ?? $this->user->getUser();
      $doctor = $consultation->getDoctor();

      $invoice = $consultation->getInvoice();
      $invoiceAmount = $consultation->getFile() ? $consultation->getFile()->getPrice() : 0;

      $exams = $consultation->getExams();
      $examBaskets = $invoice->getExamsInvoiceBaskets();
      $lab = $consultation->getLab() ?? null;
      $newLab = (new Lab())
        ->setNote($note)
        ->setUser($currentUser)
        ->setCreatedAt($createdAt)
        ->setPatient($patient)
        ->setHospital($hospital)
        ->setConsultation($consultation);
      $labResultsItems = $lab?->getLabResults();
      // Exams
      if ($exams->count() < 1 && $examBaskets->count() > 0) { // Si les examens n'existent pas
        // Mais que le panier des examens existent...
        foreach ($examBaskets as $basket)
          $this->em->remove($basket);

        if (null !== $lab) {
          $consultation->setLab(null);
          $this->em->remove($lab);
        }
        // On supprime les examens du panier
        // Ainsi que les prescription des examens au Labo
      } // Fin si...
      elseif ($exams->count() > 0 && $examBaskets->count() < 1) { // Sinon si les examens existent ?
        foreach ($exams as $exam) {
          $newExamBasket = (new ExamsInvoiceBasket())
            ->setPrice($exam->getPrice())
            ->setExam($exam)
            ->setInvoice($invoice);

          $labResults = (new LabResult())
            ->setExam($exam)
            ->setLab($lab ?? $newLab);

          $invoiceAmount += $exam->getPrice();

          $this->em->persist($labResults);
          $this->em->persist($newExamBasket);
        }
      } // Fin sinon si (1)...
      elseif ($exams->count() > 0 && $examBaskets->count() > 0) { // Si les examens existent à la fois
        // dans la consultation
        // et dans le panier
        foreach ($examBaskets as $examBasket) {
          $examJoin = $this->examRepository->find($examBasket->getExam()->getId());
          if ($exams->contains($examJoin) === false) $this->em->remove($examBasket);
        } // Boucle ( 1 )
        // Suppression des examens du panier qui n'existent plus ou modifiés

        foreach ($exams as $exam) {
          $findBasketExam = $this->examsInvoiceBasketRepository->findInvoiceExamBasket($exam, $invoice);
          $invoiceAmount += $exam->getPrice();
          if (null === $findBasketExam) {
            $newExamBasket2 = (new ExamsInvoiceBasket())
              ->setExam($exam)
              ->setInvoice($invoice)
              ->setPrice($exam->getPrice());
            $invoice->addExamsInvoiceBasket($newExamBasket2);
          } // Si l'examen n'existe pas dans le panier, on le rajoute

          $findLabExam = $this->labRepository->findLabExam($exam, $lab);
          if (null === $findLabExam) {
            $newLabResult = (new LabResult())
              ->setExam($exam)
              ->setLab($lab ?? $newLab);
            $this->em->persist($newLabResult);
          } // On ajoute l'examen dans le labo
        } // Boucle ( 2 )

        foreach ($labResultsItems as $item) {
          $findJoinLabExam = $this->examRepository->find($item->getExam());
          if ($exams->contains($findJoinLabExam) === false) $this->em->remove($item);
        }
      } // Fin sinon si (2)...
      // End Exams








      $acts = $consultation->getActs();
      $actBaskets = $invoice->getActsInvoiceBaskets();
      // Acts
      if ($acts->count() < 1 && $actBaskets->count() > 0) {
        foreach ($actBaskets as $basket2)
          $this->em->remove($basket2);
      }
      elseif ($acts->count() > 0 && $actBaskets->count() < 1) {
        foreach ($acts as $act) {
          $newActBasket = (new ActsInvoiceBasket())
            ->setPrice($act->getPrice())
            ->setInvoice($invoice)
            ->setAct($act);

          $invoiceAmount += $act->getPrice();
          $this->em->persist($newActBasket);
        }
      }
      elseif ($acts->count() > 0 && $actBaskets->count() > 0) {
        foreach ($acts as $act) {
          $findBasketAct = $this->actsInvoiceBasketRepository->findInvoiceAct($act, $invoice);
          $invoiceAmount += $act->getPrice();
          if (null === $findBasketAct) {
            $newActBasket2 = (new ActsInvoiceBasket())
              ->setAct($act)
              ->setInvoice($invoice)
              ->setPrice($act->getPrice());

            $this->em->persist($newActBasket2);
          }
        }

        foreach ($actBaskets as $actBasket) {
          $actJoin = $this->actRepository->find($actBasket->getAct()->getId());
          if ($acts->contains($actJoin) === false) $this->em->remove($actBasket);
        }
      }
      // End Acts








      $treatments = $consultation->getTreatments();
      $treatmentBaskets = $invoice->getTreatmentInvoiceBaskets();
      $nursing = $consultation->getNursing();
      $nurseTreatments = $nursing?->getNursingTreatments();
      $newNursing = (new Nursing())
        ->setConsultation($consultation)
        ->setCreatedAt($createdAt)
        ->setHospital($hospital)
        ->setPatient($patient);
      // Treatments
      if ($treatments->count() < 1 && $treatmentBaskets->count() > 0) {
        foreach ($treatmentBaskets as $basket3) $this->em->remove($basket3);

        if (null !== $nursing) {
          foreach ($nurseTreatments as $nurseTreatment) $this->em->remove($nurseTreatment);
          $this->em->remove($nursing);
        }
      }
      elseif ($treatments->count() > 0 && $treatmentBaskets->count() < 1) {
        foreach ($treatments as $treatment) {
          $newTreatmentBasket = (new TreatmentInvoiceBasket())
            ->setPrice($treatment->getPrice())
            ->setInvoice($invoice)
            ->setTreatment($treatment);

          $invoiceAmount += $treatment->getPrice();

          $nursingTreatment = (new NursingTreatment())
            ->setTreatment($treatment)
            ->setNursing($nursing ?? $newNursing);

          $this->em->persist($nursingTreatment);
          $this->em->persist($newTreatmentBasket);
        }
      }
      elseif ($treatments->count() > 0 && $treatmentBaskets->count() > 0) {
        foreach ($treatments as $treatment) {
          $invoiceAmount += $treatment->getPrice();
          $findTreatmentBasket = $this
            ->treatmentInvoiceBasketRepository
            ->findInvoiceTreatmentBasket($treatment, $invoice);
          if (null === $findTreatmentBasket) {
            $newTreatmentBasket2 = (new TreatmentInvoiceBasket())
              ->setTreatment($treatment)
              ->setInvoice($invoice)
              ->setPrice($treatment->getPrice());
            $this->em->persist($newTreatmentBasket2);
          }

          $findTreatmentNurses = $this->nursingRepository->findTreatmentNurse($treatment, $nursing);
          if (null === $findTreatmentNurses) {
            $newTreatmentNursing = (new NursingTreatment())
              ->setTreatment($treatment)
              ->setNursing($nursing ?? $newNursing);
            $this->em->persist($newTreatmentNursing);
          }
        }

        foreach ($treatmentBaskets as $treatmentBasket) {
          $treatmentJoin = $this->treatmentRepository->find($treatmentBasket->getTreatment()->getId());
          if ($treatments->contains($treatmentJoin) === false) $this->em->remove($treatmentBasket);
        }

        foreach ($nurseTreatments as $nurseTreatment) {
          $treatmentJoin2 = $this->treatmentRepository->find($nurseTreatment->getTreatment()->getId());
          if ($treatments->contains($treatmentJoin2) === false) $this->em->remove($nurseTreatment);
        }
      }
      // End Treatments






      // Handle Appointment
      $appointmentDate = $consultation->appointmentDate ?? $createdAt;
      $appointment = $consultation->getAppointment();
      $appointment->setPatient($patient);
      $appointment->setAppointmentDate($appointmentDate);
      $appointment->setDoctor($doctor);
      $appointment->setFullName($patient->getFullName());
      // End Handle Appointment






      // Handle hospitalization
      $hospitalization = $consultation->getHospitalization();
      $bed = $hospitalization?->getBed();
      $newBed = $consultation?->bed;
      $releasedAt = $consultation->hospReleasedAt ?? new DateTime();
      $hospReleasedAt = $consultation->hospReleasedAt ?? $releasedAt;
      if (null !== $bed && null !== $newBed) {
        if ($bed->getId() !== $newBed->getId()) {
          $bed->setItHasTaken(false);
          $hospitalization->setConsultation(null);
          $bed->removeHospitalization($hospitalization);
          $this->em->flush();

          $hospDaysCounter = $hospReleasedAt->diff($releasedAt)->days + 1;

          $newHosp = (new Hospitalization())
            ->setConsultation($consultation)
            ->setPrice($newBed->getPrice())
            ->setHospital($hospital)
            ->setDaysCounter($hospDaysCounter)
            ->setReleasedAt($releasedAt)
            ->setBed($newBed);

          $newBed->setItHasTaken(true);

          $hospAmount = $newBed->getPrice() * $hospDaysCounter;
          $invoice->setHospitalizationAmount($hospAmount);

          $this->em->persist($newHosp);
        }
      }
      elseif (null === $bed && null !== $newBed) {
        $hospDaysCounter = $hospReleasedAt->diff($createdAt)->days + 1;
        if ($newBed->isItHasTaken() === false) {
          $hosp = (new Hospitalization())
            ->setPrice($newBed->getPrice())
            ->setConsultation($consultation)
            ->setReleasedAt($hospReleasedAt)
            ->setBed($newBed)
            ->setDaysCounter($hospDaysCounter)
            ->setHospital($hospital);

          $hospAmount = $newBed->getPrice() * $hospDaysCounter;
          $invoice->setHospitalizationAmount($hospAmount);

          $newBed->setItHasTaken(true);
          $this->em->persist($hosp);
        }
        else throw new Exception('Ce lit n\'est pas disponible.');
      }
      elseif (null !== $bed && null === $newBed) {
        $bed->setItHasTaken(false);
        $hospitalization->setBed(null);
        $hospitalization->setConsultation(null);
        $invoice->setHospitalizationAmount(0);
      }
      elseif (null !== $hospitalization) $consultation->setHospitalization(null);
      // End Handle hospitalization


      $lab?->setPatient($patient);




      $invoice->setAmount($invoiceAmount);
      $invoice->setTotalAmount($invoiceAmount);
      $invoice->setPatient($patient);

      $this->em->flush();
    }
  }
}
