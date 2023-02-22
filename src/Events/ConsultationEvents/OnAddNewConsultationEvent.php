<?php

namespace App\Events\ConsultationEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ActsInvoiceBasket;
use App\Entity\Appointment;
use App\Entity\Consultation;
use App\Entity\ExamsInvoiceBasket;
use App\Entity\Hospitalization;
use App\Entity\Invoice;
use App\Entity\Lab;
use App\Entity\LabResult;
use App\Entity\Nursing;
use App\Entity\NursingTreatment;
use App\Entity\TreatmentInvoiceBasket;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewConsultationEvent implements EventSubscriberInterface
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

  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em)
  {
  }

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event)
  {
    $consultation = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($consultation instanceof Consultation && $method === Request::METHOD_POST) {
      $createdAt = new DateTime();
      $currentUser = $this->user->getUser();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $patient = $consultation->getPatient();
      $doctor = $consultation->getDoctor();

      $consultation->setCreatedAt($createdAt);
      $consultation->setHospital($hospital);
      $consultation->setUser($currentUser);

      $invoice = new Invoice();
      $invoiceAmount = 0;

      $invoiceAmount += $consultation->getFile() ? $consultation->getFile()->getPrice() : 0;

      $acts = $consultation->getActs();
      $treatments = $consultation->getTreatments();
      $exams = $consultation->getExams();

      $nursing = new Nursing();
      $nursingTreatment = new NursingTreatment();
      $lab = new Lab();

      // handle get acts prices
      if ($acts->count() > 0) {
        foreach ($acts as $act) {
          if (null !== $act->getPrice()) {
            $invoiceAmount += $act->getPrice();
            $actInvoiceBasket = (new ActsInvoiceBasket())
              ->setInvoice($invoice)
              ->setPrice($act->getPrice())
              ->setAct($act);
            $this->em->persist($actInvoiceBasket);
          }
        }
      } // End handle get acts prices

      // handle nursing
      if ($treatments->count() > 0) {
        foreach ($treatments as $treatment) {
          if (null !== $treatment->getPrice()) {
            $nursingTreatment->setTreatment($treatment);
            $nursingTreatment->setNursing($nursing);

            $treatmentInvoiceBasket = (new TreatmentInvoiceBasket())
              ->setInvoice($invoice)
              ->setTreatment($treatment)
              ->setPrice($treatment->getPrice());

            $invoiceAmount += $treatment->getPrice();
            $this->em->persist($treatmentInvoiceBasket);
          }
        }

        $nursing->setConsultation($consultation);

        $this->em->persist($nursing);
        $this->em->persist($nursingTreatment);
      } // End handle nursing

      // handle get exams prices
      if ($exams->count() > 0) {
        foreach ($exams as $exam) {
          if (null !== $exam->getPrice()) {
            $invoiceAmount += $exam->getPrice();
            $examInvoiceBasket = (new ExamsInvoiceBasket())
              ->setPrice($exam->getPrice())
              ->setInvoice($invoice)
              ->setExam($exam);

            $labResults = (new LabResult())
              ->setExam($exam)
              ->setLab($lab);

            $this->em->persist($labResults);
            $this->em->persist($examInvoiceBasket);
          }
        }
      } // End handle get exams prices






      // Handle hospitalization
      $bed = $consultation->bed ?? null;
      $hospReleasedAt = $consultation->hospReleasedAt ?? $createdAt;
      if (null !== $bed) {
        $hospDaysCounter = $hospReleasedAt->diff($createdAt)->days + 1;
        if ($bed->isItHasTaken() === false) {
          $hosp = (new Hospitalization())
            ->setPrice($bed->getPrice())
            ->setConsultation($consultation)
            ->setReleasedAt($hospReleasedAt)
            ->setBed($bed)
            ->setDaysCounter($hospDaysCounter)
            ->setHospital($hospital);

          $invoiceAmount += ($bed->getPrice() * $hospDaysCounter);
          $bed->setItHasTaken(true);
          $this->em->persist($hosp);
        }
        else throw new Exception('Ce lit n\'est pas disponible.');
      }
      // End Handle hospitalization





      // Handle Appointment
      $appointmentDate = $consultation->appointmentDate ?? $createdAt;
      $appointment = (new Appointment())
        ->setDoctor($doctor)
        ->setReason('Consultation médicale.')
        ->setConsultation($consultation)
        ->setUser($currentUser)
        ->setHospital($hospital)
        ->setAppointmentDate($appointmentDate)
        ->setPatient($patient)
        ->setCreatedAt($createdAt);
      // End Handle Appointment




      // Handle Consultation's Invoice
      $invoice->setPatient($patient);
      $invoice->setHospital($hospital);
      $invoice->setUser($currentUser);
      $invoice->setReleasedAt($createdAt);
      $invoice->setConsultation($consultation);
      // End Handle Consultation's Invoice


      $invoice->setAmount($invoiceAmount);
      $invoice->setTotalAmount($invoiceAmount);

      $note = $consultation->getNote() ?? null;
      $lab->setHospital($hospital);
      $lab->setUser($currentUser);
      $lab->setNote($note);
      $lab->setConsultation($consultation);
      $lab->setCreatedAt($createdAt);

      $this->em->persist($lab);
      $this->em->persist($invoice);
      $this->em->persist($appointment);

      $this->em->flush();
    }
  }
}
