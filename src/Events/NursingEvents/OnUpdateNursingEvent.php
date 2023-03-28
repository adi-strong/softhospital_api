<?php

namespace App\Events\NursingEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\InvoiceStoric;
use App\Entity\Nursing;
use App\Entity\NursingTreatment;
use App\Repository\NursingTreatmentRepository;
use App\Repository\TreatmentRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateNursingEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly NursingTreatmentRepository $repository,
    private readonly TreatmentRepository $treatmentRepository,
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
    $method = $event->getRequest()->getMethod();
    $nursing = $event->getControllerResult();
    if ($nursing instanceof Nursing && $method === Request::METHOD_PATCH) {
      $createdAt = $nursing->arrivedAt ?? new DateTime();
      $leaveAt = $nursing->leaveAt;
      $values = $nursing?->treatments;
      $treatments = $nursing->getNursingTreatments();
      $sum = $nursing->sum;
      $consult = $nursing->getConsultation();

      $patient = $nursing->getPatient();
      $nursing?->setFullName($patient?->getFullName());
      $consult?->setFullName($patient?->getFullName());
      $consult?->setIsPublished(false);
      $nursing->setConsultation($consult);

      if (null === $sum) {
        if ($nursing->isIsPublished() === true && null !== $values) {
          foreach ($values as $value) {
            $id = $value['id'] ?? 0;
            $medicines = $value['medicines'] ?? null;
            $findOne = $this->treatmentRepository->find($id);
            if (null !== $findOne) {
              $newTreatment = (new NursingTreatment())
                ->setTreatment($findOne)
                ->setMedicines($medicines)
                ->setNursing($nursing)
                ->setUser($this->user->getUser())
                ->setLeaveAt($leaveAt)
                ->setCreatedAt($createdAt);
              $nursing->addNursingTreatment($newTreatment);
            }
          }
        }
        elseif (null !== $values) {
          foreach ($treatments as $treatment) {
            $findOne = $this->repository->findNursingTreatment($treatment->getTreatment(), $nursing);
            if (null !== $findOne) {
              foreach ($values as $value) {
                $id = $value['id'] ?? 0;
                $medicines = $value['medicines'] ?? null;
                if ($treatment->getTreatment()->getId() === $id) {
                  $nursing->setIsPublished(true);
                  $findOne->setUser($this->user->getUser());
                  $findOne->setCreatedAt($createdAt);
                  $findOne->setLeaveAt($leaveAt);
                  $findOne->setMedicines($medicines);
                }
              }
            }
          }
        }
      }

      /*if (null !== $sum && $sum > 0.00) {
        if (null !== $nursing->getDiscount() && null !== $nursing->getVTA()) {
          $vTA = ($subTotal * $nursing->getVTA()) / 100;
          $discount = ($subTotal * $nursing->getDiscount()) / 100;

          $amount = $subTotal - $discount;
          $nursing->setAmount($this->roundAmount->roundAmount($amount, 2));

          $total = $amount + $vTA;
          $nursing->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        elseif (null !== $nursing->getDiscount()) {
          $discount = ($subTotal * $nursing->getDiscount()) / 100;
          $total = $subTotal - $discount;
          $nursing->setAmount($this->roundAmount->roundAmount($total, 2));
          $nursing->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        elseif (null !== $nursing->getVTA()) {
          $discount = ($subTotal * $nursing->getVTA()) / 100;
          $total = $subTotal + $discount;
          $nursing->setAmount($this->roundAmount->roundAmount($total, 2));
          $nursing->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        else $nursing->setTotalAmount($this->roundAmount->roundAmount($subTotal, 2));
        $nursing->setLeftover(
          $this
            ->roundAmount
            ->roundAmount($nursing->getTotalAmount() - $nursing->getPaid(), 2)
        );
      } */

      if (null !== $sum && $sum > 0.00) {
        $paid = $nursing->getPaid() + $sum;
        $leftOver = $nursing->getTotalAmount() - $paid;

        $nursing->setPaid($paid);
        $nursing->setLeftover($leftOver);

        $historic = (new InvoiceStoric())
          ->setCreatedAt(new DateTime())
          ->setUser($this->user->getUser())
          ->setAmount($sum)
          ->setNursing($nursing);
        $this->em->persist($historic);
      }

      $this->em->flush();

    }
  }
}
