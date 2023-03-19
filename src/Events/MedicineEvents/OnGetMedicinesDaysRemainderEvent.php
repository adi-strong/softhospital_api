<?php

namespace App\Events\MedicineEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Repository\MedicineRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnGetMedicinesDaysRemainderEvent implements EventSubscriberInterface
{
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

  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly MedicineRepository $repository)
  {
  }

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();

    if ($method === Request::METHOD_GET) {
      $medicines = $this->repository->findAll();
      $currentDate = new DateTime();

      foreach ($medicines as $medicine) {
        $releasedAt = $medicine->getReleased() ?? null;
        $expiryDate = $medicine->getExpiryDate() ?? null;
        if (null !== $releasedAt && $expiryDate !== null) {
          $daysRemainder = $expiryDate->diff($currentDate)->days;
          if (false === $medicine->isIsDeleted() && $medicine->getDaysRemainder() > 0) {
            if ($daysRemainder !== $medicine->getDaysRemainder()) {
              $medicine->setDaysRemainder($daysRemainder);
              $this->em->flush();
            }
          }
        }
      }

    }
  }
}
