<?php

namespace App\Events\InvoiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxHistoric;
use App\Entity\CovenantInvoice;
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

class OnUpdateCovenantInvoiceEvent implements EventSubscriberInterface
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
    private readonly EntityManagerInterface $em,
    private readonly BoxRepository $boxRepository,
    private readonly HandleCurrentUserService $user) { }

  public function handler(ViewEvent $event)
  {
    $invoice = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($invoice instanceof CovenantInvoice && $method === Request::METHOD_PATCH) {
      $datetime = new DateTime();
      $hosp = $this->user->getHospitalCenter() ?? $this->user->getHospital();
      $box = $this->boxRepository->findBox($hosp->getId());
      $sum = $invoice->sum ?? '0.00';
      if ($sum > 0.00) {
        $paid = $invoice->getPaid() + $sum;
        $leftover = $invoice->getTotalAmount() - $paid;
        $invoice->setPaid($paid);
        $invoice->setLeftover($leftover);

        // payment's historic
        $historic = (new InvoiceStoric())
          ->setUser($this->user->getUser())
          ->setCreatedAt($datetime)
          ->setAmount($sum)
          ->setCovenantInvoice($invoice);
        $this->em->persist($historic);
        // end payment's historic

        // handle inbox
        if (null !== $box) {
          $inBox = (new BoxHistoric())
            ->setAmount($sum)
            ->setCreatedAt($datetime)
            ->setTag('input')
            ->setBox($box);
          $this->em->persist($inBox);
        }
        // end handle inbox

      } // handle payment

      $this->em->flush();
    }
  }
}
