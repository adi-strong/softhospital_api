<?php

namespace App\Events\HospitalizationsEvent;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Repository\HospitalizationRepository;
use App\Services\RoundAmountService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnGetHospDaysRemainderEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly RoundAmountService $roundAmount,
    private readonly HospitalizationRepository $repository)
  {
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_RESPOND,
      ]
    ];
  }

  /**
   * @throws Exception
   */
  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();

    if ($method === Request::METHOD_GET) {
      $items = $this->repository->findAll();
      $currentDate = new DateTime();
      foreach ($items as $item) {
        if ($item->isIsCompleted() === false) {
          $daysCounter = $currentDate->diff(new DateTime($item->getReleasedAt()->format('Y-m-d')))->days + 1;
          //dd($daysCounter);
          $invoice = $item->getConsultation()?->getInvoice();
          if ($daysCounter > $item->getDaysCounter() && null !== $invoice) {
            $item->setDaysCounter($daysCounter);
            $hospAmount = $item->getBed()->getPrice() * $item->getDaysCounter();
            $invoice->setHospitalizationAmount($hospAmount);
            $amount = $invoice->getAmount() + $hospAmount;

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

            $this->em->flush();
          }
        }
      }

    }
  }
}
