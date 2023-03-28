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
use App\Repository\ActsInvoiceBasketRepository;
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
    private readonly ActsInvoiceBasketRepository $actsInvoiceBasketRepository,
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
      if ($consult->isIsPublished() === true) {
        $currentUser = $this->user->getUser();
        $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
        $createdAt = $consult->getCreatedAt() ?? new DateTime();

        $patient = $consult->getPatient();
        $fullName = $patient->getFullName();
        $agent = $consult->getDoctor();

        $consult->setFullName($fullName);
        $consult->setCreatedAt($createdAt);

        $invoice = $consult->getInvoice();
        $invoice->setFullName($fullName);
        $file = $consult->getFile();
        $invoiceAmount = $file ? $file->getPrice() : 0;

        // appointment
        $appointmentDate = $consult->appointmentDate ?? $createdAt;
        $appointment = $consult->getAppointment();
        $appointment->setPatient($patient);
        $appointment->setFullName($fullName);
        $appointment->setDoctor($agent);
        $appointment->setAppointmentDate($appointmentDate);
        // end appointment

        // acts
        $acts = $consult->getActs();
        $actBaskets = $invoice->getActsInvoiceBaskets();
        if ($acts->count() < 1) {
          if ($actBaskets->count() > 0) {
            foreach ($actBaskets as $basket) $this->em->remove($basket);
          }
        }
        else {
          if ($actBaskets->count() > 0 && $acts->count() > 0) {
            foreach ($acts as $act) {
              $findAct = $this->actsInvoiceBasketRepository->findInvoiceAct($act, $invoice);
              if ($findAct === null) {
                $actBasket = (new ActsInvoiceBasket())
                  ->setInvoice($invoice)
                  ->setAct($act)
                  ->setPrice($act->getPrice());
                $this->em->persist($actBasket);
              }
            } // 1st si les actes n'existent pas dans le panier, on les ajoutes.

            foreach ($actBaskets as $basket) {
              $act = $basket->getAct();
              if (null !== $act && $acts->contains($act) === false) $this->em->remove($basket);
            }
            // 2nd : on vérifie s'il y a cohérence entre les actes de la consultation et ceux du panier
            // s'il n'y a pas d'occurence on supprime

          } // si les actes existent déjà dans le panier ? => on évite les doublons...

          if ($actBaskets->count() < 1) {
            foreach ($acts as $act) {
              $actBasket = (new ActsInvoiceBasket())
                ->setPrice($act->getPrice())
                ->setAct($act)
                ->setInvoice($invoice);
              $this->em->persist($actBasket);
            }
          } // si les actes n'existent pas ? => on les ajoutent tout simplement

          foreach ($acts as $act) {
            $invoiceAmount += $act->getPrice();
          } // ...
        }
        // end acts

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

        // treatments
        $treatments = $consult->getTreatments();
        $nursing = $consult->getNursing();
        if ($treatments->count() < 1) {
          $consult->setNursing(null);
          if (null !== $nursing) {
            $this->em->remove($nursing);
          }
        }
        else {
          if (null !== $nursing) {
            $nursing->setPatient($patient);
            $nursing->setFullName($fullName);

            $nurses = $nursing->getNursingTreatments();
            if ($nurses->count() > 0 && $treatments->count() > 0) {
              foreach ($treatments as $treatment) {
                $findTr = $this->nursingTreatmentRepository->findNursingTreatment($treatment, $nursing);
                if (null === $findTr) {
                  $nurse = (new NursingTreatment())
                    ->setPrice($treatment->getPrice())
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
                  ->setPrice($treatment->getPrice())
                  ->setTreatment($treatment)
                  ->setNursing($nursing);
                $this->em->persist($nurse);
              }
            } // s'ils n'existent pas, on les ajoute.

            $nurseAmount = 0;
            foreach ($treatments as $treatment) {
              $nurseAmount += $treatment->getPrice();
            }

            $nursing->setSubTotal($nurseAmount);
            $nursing->setAmount($nurseAmount);
            $nursing->setTotalAmount($nurseAmount);
          } // si le nursing existe

          if (null === $nursing && $treatments->count() > 0) {
            $newNursing = (new Nursing())
              ->setFullName($fullName)
              ->setPatient($patient)
              ->setHospital($hospital)
              ->setConsultation($consult)
              ->setCreatedAt($createdAt);

            $nurseAmount = 0;
            foreach ($treatments as $treatment) {
              $nurseAmount += $treatment->getPrice();
              $nurse = (new NursingTreatment())
                ->setNursing($newNursing)
                ->setTreatment($treatment)
                ->setPrice($treatment->getPrice());
              $this->em->persist($nurse);
            }

            $newNursing->setTotalAmount($nurseAmount);
            $newNursing->setSubTotal($nurseAmount);
            $newNursing->setAmount($nurseAmount);
            $this->em->persist($newNursing);
          }
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

        $invoice->setSubTotal($invoiceAmount);
        $invoice->setAmount($invoiceAmount);
        $invoice->setTotalAmount($invoiceAmount);
        $invoice->setLeftover($invoiceAmount);

        // we finish here
        $this->em->flush();
        // **********************************************************************************************
      }

      $diagnostic = trim($consult?->getDiagnostic(), ' ');
      $date = (new DateTime())->format('Y-m-d');
      $followed = $consult?->getFollowed();

      if ($followed === null) {
        $followed[] = [
          'date' => $date,
          'diagnostic' => $diagnostic,
          'temperature' => $consult->getTemperature(),
          'weight' => $consult->getWeight(),
          'arterialTension' => $consult->getArterialTension(),
          'cardiacFrequency' => $consult->getCardiacFrequency(),
          'respiratoryFrequency' => $consult->getRespiratoryFrequency(),
          'oxygenSaturation' => $consult->getOxygenSaturation()];
      }
      else {
        $followed[] = array_push($followed, [
          'date' => $date,
          'diagnostic' => $diagnostic,
          'temperature' => $consult->getTemperature(),
          'weight' => $consult->getWeight(),
          'arterialTension' => $consult->getArterialTension(),
          'cardiacFrequency' => $consult->getCardiacFrequency(),
          'respiratoryFrequency' => $consult->getRespiratoryFrequency(),
          'oxygenSaturation' => $consult->getOxygenSaturation()]);
      }

      $consult->setFollowed($followed);
      $consult->setDiagnostic(null);
      $consult->setTemperature(null);
      $consult->setWeight(null);
      $consult->setArterialTension(null);
      $consult->setCardiacFrequency(null);
      $consult->setRespiratoryFrequency(null);
      $consult->setOxygenSaturation(null);
    }

  }
}
