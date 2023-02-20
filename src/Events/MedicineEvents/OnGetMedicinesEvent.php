<?php

namespace App\Events\MedicineEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Medicine;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnGetMedicinesEvent implements EventSubscriberInterface
{
  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_DESERIALIZE,
      ]
    ];
  }

  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $medicine = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($medicine instanceof Medicine && $method === Request::METHOD_GET) {
      $released = $medicine->getReleased() ?? null;
      $expiryDate = $medicine->getExpiryDate();

      if ($medicine->isIsDeleted() !== false) {

        if (null !== $released) {
          $days = $expiryDate->diff($released)->days + 1;
          $medicine->setDaysRemainder($days);

          if ($days < 1) {
            $medicine->setReleased(null);
            $medicine->setExpiryDate(null);
            $medicine->setDaysRemainder(0);
          }

          $this->em->flush();
        }

      }

    }
  }
}
