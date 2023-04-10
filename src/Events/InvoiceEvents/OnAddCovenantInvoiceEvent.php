<?php

namespace App\Events\InvoiceEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\CovenantInvoice;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddCovenantInvoiceEvent implements EventSubscriberInterface
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

  public function __construct(private readonly HandleCurrentUserService $user) { }

  public function handler(ViewEvent $event)
  {
    $invoice = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($invoice instanceof CovenantInvoice && $method === Request::METHOD_POST) {
      $invoice->setUser($this->user->getUser());
      $invoice->setReleasedAt(new DateTime());
      $invoice->setTotalAmount($invoice->getSubTotal());
      $invoice->setLeftover($invoice->getSubTotal());
    }
  }
}
