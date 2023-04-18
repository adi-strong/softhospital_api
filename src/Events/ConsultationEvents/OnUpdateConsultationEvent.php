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
use App\Repository\ActRepository;
use App\Repository\ExamsInvoiceBasketRepository;
use App\Repository\LabResultRepository;
use App\Repository\NursingTreatmentRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateConsultationEvent implements EventSubscriberInterface
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
    private readonly ActRepository $actRepository,
    private readonly ExamsInvoiceBasketRepository $examsInvoiceBasketRepository,
    private readonly LabResultRepository $labResultRepository,
    private readonly NursingTreatmentRepository $nursingTreatmentRepository,
    private readonly EntityManagerInterface $em)
  {
  }

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event) {
    $consult = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($consult instanceof Consultation && $method === Request::METHOD_PATCH)
    {
      $invoice = $consult->getInvoice();
      $file = $consult->getFile();
      $invoiceAmount = $file ? $file->getPrice() : 0;
      $nursing = $consult->getNursing();
      $patient = $consult->getPatient();
      $fullName = $patient->getFullName();
      $createdAt = $consult->getCreatedAt() ?? new DateTime();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $actBaskets = $invoice->getActsInvoiceBaskets();

      if ($consult->isIsPublished() === true) {
        $currentUser = $this->user->getUser();

        $agent = $consult->getDoctor();

        $consult->setFullName($fullName);
        $consult->setCreatedAt($createdAt);

        $invoice->setFullName($fullName);

        // appointment
        $appointmentDate = $consult->appointmentDate ?? $createdAt;
        $appointment = $consult->getAppointment();
        $appointment->setPatient($patient);
        $appointment->setFullName($fullName);
        $appointment->setDoctor($agent);
        $appointment->setAppointmentDate($appointmentDate);
        // end appointment

        // acts
        foreach ($actBaskets as $act) $invoiceAmount += $act->getPrice();

        // end acts

        // treatments
        $treatments = $consult->getTreatments();
        $actItems = $consult->actsItems;
        $nurseActs = $nursing->getActs();
        $dateTime = (new DateTime())->format('Y-m-d');
        $nursingAmount = 0;

        if (null !== $nursing) {
          $nursing->setPatient($patient);
          $nursing->setFullName($fullName);

          $nurses = $nursing->getNursingTreatments();
          if ($nurses->count() > 0 && $treatments->count() > 0) {
            foreach ($treatments as $treatment) {
              $findTr = $this->nursingTreatmentRepository->findNursingTreatment($treatment, $nursing);
              if (null === $findTr) {
                $nurse = (new NursingTreatment())
                  ->setTreatment($treatment)
                  ->setNursing($nursing);
                $this->em->persist($nurse);
              } // ...on les ajoute.

              foreach ($nurses as $nurse) {
                $treatment = $nurse->getTreatment();
                if (null !== $treatment && $treatments->contains($treatment) === false) $this->em->remove($nurse);
              } // si les treatments ne correspondent pas avec ceux de la fiche
              // ...on les supprime

            } // si les traitements ne sont pas dans le nursing
          } // si les items des treatments existent...

          if ($nurses->count() < 1) {
            foreach ($treatments as $treatment) {
              $nurse = (new NursingTreatment())
                ->setTreatment($treatment)
                ->setNursing($nursing);
              $this->em->persist($nurse);
            }
          } // s'ils n'existent pas, on les ajoute.

          if (null !== $actItems) {
            foreach ($actItems as $act) {
              $id = $act['id'] ?? 0;
              $item = $this->actRepository->find($id);
              if (null !== $item) {
                $invoiceAmount += $item->getPrice();
                $consult->addAct($item);
                $actBasketInvoice = (new ActsInvoiceBasket())
                  ->setPrice($item->getPrice())
                  ->setAct($item)
                  ->setInvoice($invoice);

                $nurseActs[] = [
                  'releasedAt' => $dateTime,
                  'wording' => $item->getWording(),
                  'procedures' => $item->getProcedures(),
                  'isDone' => false
                ];

                $this->em->persist($actBasketInvoice);
              }
            }
            $nursing->setActs($nurseActs);
          }

          foreach ($treatments as $treatment) $nursingAmount += $treatment->getPrice();

          $nursing->setSubTotal($nursingAmount);
          $nursing->setTotalAmount($nursingAmount);
          $nursing->setAmount($nursingAmount);
          $nursing->setCurrency($parameter[0] ?? null);
        } // si le nursing existe

        if (null === $nursing) {
          $newNursing = (new Nursing())
            ->setFullName($fullName)
            ->setPatient($patient)
            ->setHospital($hospital)
            ->setConsultation($consult)
            ->setCurrency($parameter[0] ?? null)
            ->setCreatedAt($createdAt);

          foreach ($treatments as $treatment) {
            $nursingAmount += $treatment->getPrice();
            $nurse = (new NursingTreatment())
              ->setNursing($newNursing)
              ->setTreatment($treatment);
            $this->em->persist($nurse);
          }

          if (null !== $actItems) {
            foreach ($actItems as $actItem) {
              $id = $actItem['id'] ?? 0;
              $item = $this->actRepository->find($id);
              if (null !== $item) {
                $invoiceAmount += $item->getPrice();
                $consult->addAct($item);
                $actBasketInvoice = (new ActsInvoiceBasket())
                  ->setAct($item)
                  ->setInvoice($invoice)
                  ->setPrice($item->getPrice());

                $nurseActs[] = [
                  'releasedAt' => $dateTime,
                  'wording' => $item->getWording(),
                  'procedures' => $item->getProcedures(),
                  'isDone' => false
                ];

                $this->em->persist($actBasketInvoice);
              }
            }
            $newNursing->setActs($nurseActs);
          }

          $newNursing->setSubTotal($nursingAmount);
          $newNursing->setTotalAmount($nursingAmount);
          $newNursing->setAmount($nursingAmount);
          $this->em->persist($newNursing);
        }
        // end treatments

        // hospitalization
        $hospReleasedAt = $consult->hospReleasedAt ?? $createdAt;
        $bed = $consult->bed;
        $hosp = $consult->getHospitalization();
        if (null !== $hosp) {
          $hosp->setFullName($fullName);
          $oldBed = $hosp->getBed();

          if (null !== $bed && $bed->getId() !== $oldBed?->getId()) {
            if ($bed->isItHasTaken() === false) {
              $oldBed?->setItHasTaken(false);
              $bed->setItHasTaken(true);

              $counter = (new DateTime())->diff($hospReleasedAt)->days + 1;
              $price = $bed->getPrice() * $counter;
              $invoiceAmount += $price;
              $bed->setItHasTaken(true);

              $hosp->setReleasedAt($hospReleasedAt);
              $hosp->setDaysCounter($counter);
              $hosp->setPrice($price);
              $hosp->setBed($bed);
            }
            else throw new Exception('Ce lit est déjà prit.');
          } // si l'hospitalisation est renseigné, on libère l'ancien lit...

          if (null !== $bed && $bed->getId() === $oldBed?->getId()) {
            $counter = (new DateTime())->diff($hospReleasedAt)->days + 1;
            $price = $oldBed->getPrice() * $counter;
            $invoiceAmount += $price;

            $hosp->setReleasedAt($hospReleasedAt);
            $hosp->setDaysCounter($counter);
            $hosp->setPrice($price);
          }

          if (null === $bed) {
            $consult->setHospitalization(null);
            $oldBed->setItHasTaken(false);
            $this->em->remove($hosp);
          }
        } // si l'ospitalisation existe déjà
        else {
          if (null !== $bed) {
            if ($bed->isItHasTaken() === false) {
              $counter = (new DateTime())->diff($hospReleasedAt)->days + 1;
              $price = $bed->getPrice() * $counter;
              $invoiceAmount += $price;
              $bed->setItHasTaken(true);
              $newHosp = (new Hospitalization())
                ->setFullName($fullName)
                ->setHospital($hospital)
                ->setConsultation($consult)
                ->setReleasedAt($hospReleasedAt)
                ->setDaysCounter($counter)
                ->setPrice($price)
                ->setBed($bed);
              $this->em->persist($newHosp);
            }
            else throw new Exception('Ce lit est déjà prit.');
          }
        }
        // end hospitalization
      }
      else {
        $actItems = $consult->actsItems;
        $acts = $nursing->getActs();
        $dateTime = (new DateTime())->format('Y-m-d');
        if (null !== $nursing) {
          foreach ($actItems as $actItem) {
            $id = $actItem['id'] ?? 0;
            $item = $this->actRepository->find($id);
            if (null !== $item) {
              $invoiceAmount += $item->getPrice();
              $consult->addAct($item);
              $actBasketInvoice = (new ActsInvoiceBasket())
                ->setPrice($item->getPrice())
                ->setAct($item)
                ->setInvoice($invoice);

              $acts[] = [
                'releasedAt' => $dateTime,
                'wording' => $item->getWording(),
                'procedures' => $item->getProcedures(),
                'isDone' => false
              ];

              $this->em->persist($actBasketInvoice);
            }
          }
          $nursing->setActs($acts);
        }
        else {
          $newNursing = (new Nursing())
            ->setConsultation($consult)
            ->setCurrency($parameter[0] ?? null)
            ->setPatient($patient)
            ->setFullName($fullName)
            ->setCreatedAt($createdAt)
            ->setHospital($hospital);
          foreach ($actItems as $actItem) {
            $id = $actItem['id'] ?? 0;
            $item = $this->actRepository->find($id);
            if (null !== $item) {
              $invoiceAmount += $item->getPrice();
              $consult->addAct($item);
              $actBasketInvoice = (new ActsInvoiceBasket())
                ->setPrice($item->getPrice())
                ->setAct($item)
                ->setInvoice($invoice);

              $acts[] = [
                'releasedAt' => $dateTime,
                'wording' => $item->getWording(),
                'procedures' => $item->getProcedures(),
                'isDone' => false
              ];

              $this->em->persist($actBasketInvoice);
            }
          }
          $newNursing->setActs($acts);
        }
      }

      foreach ($actBaskets as $act) $invoiceAmount += $act->getPrice();

      $diagnostic = null !== $consult->getDiagnostic() ? trim($consult?->getDiagnostic(), ' ') : null;
      $followed = $consult?->getFollowed();

      if (null !== $diagnostic) {
        $followed[] = [
          'date' => (new DateTime())->format('Y-m-d'),
          'diagnostic' => $diagnostic,
          'temperature' => $consult->getTemperature(),
          'weight' => $consult->getWeight(),
          'arterialTension' => $consult->getArterialTension(),
          'cardiacFrequency' => $consult->getCardiacFrequency(),
          'respiratoryFrequency' => $consult->getRespiratoryFrequency(),
          'oxygenSaturation' => $consult->getOxygenSaturation()];
      }
      else {
        $followed[] = [
          'date' => (new DateTime())->format('Y-m-d'),
          'temperature' => $consult->getTemperature(),
          'weight' => $consult->getWeight(),
          'arterialTension' => $consult->getArterialTension(),
          'cardiacFrequency' => $consult->getCardiacFrequency(),
          'respiratoryFrequency' => $consult->getRespiratoryFrequency(),
          'oxygenSaturation' => $consult->getOxygenSaturation()];
      }

      $consult->setFollowed($followed);
      $consult->setDiagnostic(null);

      // exams
      $exams = $consult->getExams();
      $examBaskets = $invoice->getExamsInvoiceBaskets();
      $lab = $consult->getLab();
      if ($exams->count() < 1) {
        $consult->setLab(null);
        if (null !== $lab) {
          $this->em->remove($lab);
        } // on traite les données relatifs au labo

        foreach ($examBaskets as $basket) $this->em->remove($basket);
      }
      else {
        if (null !== $lab) {
          $lab->setFullName($fullName);
          $lab->setPatient($patient);
          $lab->setNote($consult->getNote());

          $labResults = $lab->getLabResults();
          if ($labResults->count() > 0 && $exams->count() > 0) {
            foreach ($exams as $exam) {
              $findExam = $this->labResultRepository->findLabExam($exam, $lab);
              if ($findExam === null) {
                $labResult = (new LabResult())
                  ->setExam($exam)
                  ->setLab($lab);
                $this->em->persist($labResult);
              }
            }

            foreach ($labResults as $labResult) {
              $exam = $labResult->getExam();
              if (null !== $exam && $exams->contains($exam) === false) $this->em->remove($labResult);
            }
          }

          if ($labResults->count() < 1) {
            foreach ($exams as $exam) {
              $examBasket = (new ExamsInvoiceBasket())
                ->setInvoice($invoice)
                ->setPrice($exam->getPrice())
                ->setExam($exam);
              $this->em->persist($examBasket);
            }
          }
        } // si les examens existent dans le labo => on évite les doublons

        if (null === $lab && $exams->count() > 0) {
          $newLab = (new Lab())
            ->setCreatedAt($createdAt)
            ->setUser($currentUser)
            ->setHospital($hospital)
            ->setFullName($fullName)
            ->setPatient($patient)
            ->setConsultation($consult)
            ->setNote($consult->getNote());
          $this->em->persist($newLab);

          foreach ($exams as $exam) {
            $labResult = (new LabResult())
              ->setExam($exam)
              ->setLab($newLab);
            $this->em->persist($labResult);
          }
        }
        // si le labo n'existe pas, on le crée.

        if ($examBaskets->count() > 0 && $exams->count() > 0) {
          foreach ($exams as $exam) {
            $findExam = $this->examsInvoiceBasketRepository->findInvoiceExamBasket($exam, $invoice);
            if (null === $findExam) {
              $examBasket = (new ExamsInvoiceBasket())
                ->setExam($exam)
                ->setPrice($exam->getPrice())
                ->setInvoice($invoice);
              $this->em->persist($examBasket);
            }
          }
          // si les examens existent mais pas dans le panier, on les ajoute.

          foreach ($examBaskets as $basket) {
            $exam = $basket->getExam();
            if (null !== $exam && $exams->contains($exam) === false) $this->em->remove($basket);
          }
          // si les examens du panier n'existent pas dans la fiche, on les supprime.
        }

        if ($examBaskets->count() < 1) {
          foreach ($exams as $exam) {
            $examBasket = (new ExamsInvoiceBasket())
              ->setInvoice($invoice)
              ->setPrice($exam->getPrice())
              ->setExam($exam);
            $this->em->persist($examBasket);
          }
        }

        foreach ($exams as $exam) {
          $invoiceAmount += $exam->getPrice();
        }
      }
      // end exams

      // we finish here

      $invoice->setSubTotal($invoiceAmount);
      $invoice->setAmount($invoiceAmount);
      $invoice->setTotalAmount($invoiceAmount);
      $invoice->setLeftover($invoiceAmount);

      $this->em->flush();
      // **********************************************************************************************
    }

  }
}
