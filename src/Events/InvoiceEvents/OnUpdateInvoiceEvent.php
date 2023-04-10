<?php

namespace App\Events\InvoiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Activities;
use App\Entity\BoxHistoric;
use App\Entity\Invoice;
use App\Entity\InvoiceStoric;
use App\Repository\BoxRepository;
use App\Services\HandleCurrentUserService;
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
    private readonly HandleCurrentUserService $user) { }

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
      $isBedroomLeaved = $invoice->isBedroomLeaved;
      $isIsComplete = $invoice->isIsComplete();
      $daysCounter = $invoice->daysCounter;
      $sum = (float) $invoice->sum;

      $currentDate = new DateTime();
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();

      $consult = $invoice->getConsultation();
      $hosp = $consult?->getHospitalization();
      $patient = $invoice->getPatient();

      $invoice->setFullName($patient->getFullName());
      $invoice->setIsPublished(true);
      $consult?->setIsPublished(false);

      if ($sum > 0.00 && $sum) {
        if (null === $daysCounter) {
          $price = $hosp->getBed()->getPrice() * $daysCounter;
          $hosp->setPrice($price);
          $hosp->setFullName($patient?->getFullName());
          $hosp->setDaysCounter($daysCounter);
        }

        $consult?->setFullName($patient->getFullName());

        $paid = $invoice->getPaid() + $sum;
        $leftover = $invoice->getTotalAmount() - $paid;
        $invoice->setPaid($paid);
        $invoice->setLeftover($leftover);

        $invoiceStoric = (new InvoiceStoric())
          ->setCreatedAt($currentDate)
          ->setUser($this->user->getUser())
          ->setAmount($sum)
          ->setInvoice($invoice);
        $this->em->persist($invoiceStoric);

        $box = $this->boxRepository->findBox($hospital->getId());
        if (null !== $box) {
          $boxHistoric = (new BoxHistoric())
            ->setAmount($sum)
            ->setBox($box)
            ->setCreatedAt($currentDate)
            ->setTag('input');
          $this->em->persist($boxHistoric);
        }

        $activity = (new Activities())
          ->setDescription("Paiement d'une facture par : ".$patient->getFullName())
          ->setAuthor($this->user->getUser())
          ->setCreatedAt(new DateTime())
          ->setTitle("Paiement");
        $this->em->persist($activity);
      }
      elseif ($patient->getCovenant() !== null) {
        if (null === $daysCounter) {
          $price = $hosp->getBed()->getPrice() * $daysCounter;
          $hosp->setPrice($price);
          $hosp->setFullName($patient?->getFullName());
          $hosp->setDaysCounter($daysCounter);
        }

        $consult?->setFullName($patient->getFullName());

        $paid = $invoice->getPaid() + $sum;
        $leftover = $invoice->getTotalAmount() - $paid;
        $invoice->setPaid($paid);
        $invoice->setLeftover($leftover);

        $invoiceStoric = (new InvoiceStoric())
          ->setCreatedAt($currentDate)
          ->setUser($this->user->getUser())
          ->setAmount($sum)
          ->setInvoice($invoice);
        $this->em->persist($invoiceStoric);

        $box = $this->boxRepository->findBox($hospital->getId());
        if (null !== $box) {
          $boxHistoric = (new BoxHistoric())
            ->setAmount($sum)
            ->setBox($box)
            ->setCreatedAt($currentDate)
            ->setTag('input');
          $this->em->persist($boxHistoric);
        }

        $activity = (new Activities())
          ->setDescription("Paiement d'une facture par : ".$patient->getFullName())
          ->setAuthor($this->user->getUser())
          ->setCreatedAt(new DateTime())
          ->setTitle("Paiement");
        $this->em->persist($activity);
      }

      if ($isBedroomLeaved === true || $isIsComplete === true) {
        $hosp?->setLeaveAt($currentDate);
        $hosp?->setIsCompleted(true);
        $hosp?->getBed()->setItHasTaken(false);
      }

      if ($isIsComplete === true) {
        $consult?->setIsComplete(true);
        $consult?->setIsPublished(false);
        if (null !== $hosp)
        {
          $hosp->setLeaveAt(new DateTime());
          $hosp->setIsCompleted(true);
          $hosp->getBed()->setItHasTaken(false);
        }
      }

      $this->em->flush();
    }
  }
}
