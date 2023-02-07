<?php

namespace App\Events\TreatmentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\TreatmentCategory;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnDeleteTreatmentCategoryEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $category = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($category instanceof TreatmentCategory && $method === Request::METHOD_DELETE) {
      $treatments = $category->getTreatments();
      if ($treatments->count() > 0) {
        foreach ($treatments as $treatment)
          $category->removeTreatment($treatment);

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