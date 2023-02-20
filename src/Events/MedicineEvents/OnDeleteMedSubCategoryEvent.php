<?php

namespace App\Events\MedicineEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\MedicineSubCategories;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnDeleteMedSubCategoryEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    $category = $event->getControllerResult();
    if ($category instanceof MedicineSubCategories && $method === Request::METHOD_DELETE) {
      $category->setCategory(null);
      $medicines = $category->getMedicines();
      if ($medicines->count() > 0) {
        foreach ($medicines as $medicine)
          $category->removeMedicine($medicine);

        $this->em->flush();
      }
    }
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
}
