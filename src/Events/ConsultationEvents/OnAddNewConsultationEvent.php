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
use App\Repository\ParametersRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
    private readonly ParametersRepository $parametersRepository,
    private readonly EntityManagerInterface $em)
  {
  }

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event)
  {
    $consult = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($consult instanceof Consultation && $method === Request::METHOD_POST)
    {
      $currentUser = $this->user->getUser();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $createdAt = new DateTime();
      $parameter = $this->parametersRepository->findLastParameter($hospital);

      $patient = $consult->getPatient();
      $fullName = $patient->getFullName();
      $agent = $consult->getDoctor();

      $consult->setCreatedAt($createdAt);
      $consult->setUser($currentUser);
      $consult->setHospital($hospital);
      $consult->setFullName($fullName);

      $invoice = (new Invoice())
        ->setCurrency($parameter[0] ?? null)
        ->setUser($currentUser)
        ->setFullName($fullName)
        ->setReleasedAt($createdAt)
        ->setPatient($patient)
        ->setHospital($hospital)
        ->setConsultation($consult);
      $file = $consult->getFile();
      $invoiceAmount = $file ? $file->getPrice() : 0;

      // appointment
      $appointmentDate = $consult->appointmentDate ?? $createdAt;
      $appointment = (new Appointment())
        ->setHospital($hospital)
        ->setConsultation($consult)
        ->setPatient($patient)
        ->setFullName($fullName)
        ->setUser($currentUser)
        ->setCreatedAt($createdAt)
        ->setDoctor($agent)
        ->setAppointmentDate($appointmentDate)
        ->setReason('Consultation');
      $this->em->persist($appointment);
      // end appointment

      // acts
      $acts = $consult->getActs();
      if ($acts->count() > 0) {
        foreach ($acts as $act) {
          $invoiceAmount += $act->getPrice();
          $actBasket = (new ActsInvoiceBasket())
            ->setPrice($act->getPrice())
            ->setInvoice($invoice)
            ->setAct($act);
          $this->em->persist($actBasket);
        }
      }
      // end acts

      // exams
      $exams = $consult->getExams();
      if ($exams->count() > 0) {
        // on crée le Labo
        $lab = (new Lab())
          ->setCreatedAt($createdAt)
          ->setUser($currentUser)
          ->setHospital($hospital)
          ->setFullName($fullName)
          ->setPatient($patient)
          ->setConsultation($consult)
          ->setNote($consult->getNote());
        $this->em->persist($lab);
        // fin : on crée le Labo

        foreach ($exams as $exam) {
          $invoiceAmount += $exam->getPrice();
          $examBasket = (new ExamsInvoiceBasket())
            ->setInvoice($invoice)
            ->setPrice($exam->getPrice())
            ->setExam($exam);
          $this->em->persist($examBasket);

          $labResult = (new LabResult())
            ->setExam($exam)
            ->setLab($lab);
          $this->em->persist($labResult);
        }
      }
      // end exams

      // treatments
      $treatments = $consult->getTreatments();
      if ($treatments->count() > 0) {
        $nursing = (new Nursing())
          ->setConsultation($consult)
          ->setPatient($patient)
          ->setFullName($fullName)
          ->setCreatedAt($createdAt)
          ->setHospital($hospital);

        $nurseAmount = 0;
        foreach ($treatments as $treatment) {
          $nurseAmount += $treatment->getPrice();
          $nurse = (new NursingTreatment())
            ->setPrice($treatment->getPrice())
            ->setNursing($nursing)
            ->setTreatment($treatment);
          $this->em->persist($nurse);
        }

        $nursing->setSubTotal($nurseAmount);
        $nursing->setAmount($nurseAmount);
        $nursing->setTotalAmount($nurseAmount);
        $this->em->persist($nursing);
      }
      // end treatments

      // hospitalization
      $hospReleasedAt = $consult->hospReleasedAt ?? $createdAt;
      $bed = $consult->bed;
      if (null !== $bed) {
        if ($bed->isItHasTaken() === false) {
          $counter = $createdAt->diff($hospReleasedAt)->days + 1;
          $price = (float) $bed->getPrice() * $counter;
          $invoiceAmount += $price;
          $bed->setItHasTaken(true);
          $hosp = (new Hospitalization())
            ->setFullName($fullName)
            ->setHospital($hospital)
            ->setConsultation($consult)
            ->setReleasedAt($hospReleasedAt)
            ->setDaysCounter($counter)
            ->setPrice($price)
            ->setBed($bed);
          $this->em->persist($hosp);
        }
        else throw new Exception('Ce lit est déjà prit.');
      }
      // end hospitalization

      // we finish here
      $invoice->setSubTotal($invoiceAmount);
      $invoice->setAmount($invoiceAmount);
      $invoice->setTotalAmount($invoiceAmount);
      $invoice->setLeftover($invoiceAmount);
      $this->em->persist($invoice);
      $this->em->flush();
      // **********************************************************************************************
    }

  }
}
