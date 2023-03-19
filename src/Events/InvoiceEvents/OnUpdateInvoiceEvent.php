<?php

namespace App\Events\InvoiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxHistoric;
use App\Entity\Invoice;
use App\Entity\InvoiceStoric;
use App\Repository\BoxRepository;
use App\Services\HandleCurrentUserService;
use App\Services\RoundAmountService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateInvoiceEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly BoxRepository $boxRepository,
    private readonly RoundAmountService $roundAmount,
    private readonly HandleCurrentUserService $user)
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
    $invoice = $event->getControllerResult();

    if ($invoice instanceof Invoice && $method === Request::METHOD_PATCH) {
      $sum = $invoice->sum;
      $consult = $invoice->getConsultation();
      $isBedroomLeaved = $invoice->isBedroomLeaved;
      $isComplete = $invoice->isIsComplete();

      $hospital = $invoice->getHospital();
      $box = $this->boxRepository->findBox($hospital->getId());

      $patient = $invoice->getPatient();
      $amount = $invoice->getAmount() + $invoice->getHospitalizationAmount();

      /*if ($sum <= 0.00 && null !== $invoice->getDiscount() && null !== $invoice->getVTA()) {
        $invoice->setDiscount(null);
        $invoice->setVTA(null);
      }
      elseif ($sum <= 0.00 && null !== $invoice->getDiscount()) $invoice->setDiscount(null);
      elseif ($sum <= 0.00 && null !== $invoice->getVTA()) $invoice->setVTA(null);*/

      if ($sum > 0.00) {
        if (null !== $invoice->getDiscount() && null !== $invoice->getVTA()) {
          $vTA = ($amount * $invoice->getVTA()) / 100;
          $discount = ($amount * $invoice->getDiscount()) / 100;
          $total = ($amount + $vTA) + ($amount - $discount);
          $invoice->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        elseif (null !== $invoice->getDiscount()) {
          $discount = ($amount * $invoice->getDiscount()) / 100;
          $total = $amount - $discount;
          $invoice->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        elseif (null !== $invoice->getVTA()) {
          $discount = ($amount * $invoice->getVTA()) / 100;
          $total = $amount + $discount;
          $invoice->setTotalAmount($this->roundAmount->roundAmount($total, 2));
        }
        else $invoice->setTotalAmount($this->roundAmount->roundAmount($amount, 2));
        $invoice->setLeftover(
          $this
            ->roundAmount
            ->roundAmount($invoice->getTotalAmount() - $invoice->getPaid(), 2)
        );
      }

      if ($sum > 0.00) {
        $paid = $invoice->getPaid() + $sum;
        $leftOver = $invoice->getTotalAmount() - $paid;

        $invoice->setFullName($patient->getFullName());
        $consult->setIsComplete(true);
        $consult->setFullName($patient->getFullName());

        if ($paid <= $invoice->getTotalAmount()) {
          $invoice->setPaid($paid);
          $invoice->setLeftover($leftOver);

          $invoice->setLeftover($invoice->getTotalAmount() - $paid);

          $historic = (new InvoiceStoric())
            ->setCreatedAt(new DateTime())
            ->setUser($this->user->getUser())
            ->setAmount($sum)
            ->setInvoice($invoice);
          $this->em->persist($historic);

          if (null !== $box) {
            $boxHistoric = (new BoxHistoric())
              ->setBox($box)
              ->setCreatedAt(new DateTime())
              ->setAmount($sum)
              ->setTag('input');
            $this->em->persist($boxHistoric);
          }
        }
      }

      if ($isComplete === true || $isBedroomLeaved === true) {
        $hosp = $consult->getHospitalization() ?? null;
        if (null !== $hosp) {
          $leaveAt = new DateTime();
          $hosp->setLeaveAt($leaveAt);
          $hosp->getBed()->setItHasTaken(false);
          $hosp->setIsCompleted(true);
        }
      }

      if ($isComplete === true) {
        $consult->setIsComplete(true);
        $invoice->setUpdatedAt(new DateTime());
      }

      $this->em->flush();
    }
  }
}
