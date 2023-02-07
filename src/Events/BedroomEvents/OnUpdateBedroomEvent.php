<?php

namespace App\Events\BedroomEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Bedroom;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateBedroomEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $bedroom = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($bedroom instanceof Bedroom && $method === Request::METHOD_PATCH) {
      if ($bedroom->isIsDeleted() === true) {
        $beds = $bedroom->getBeds();
        if ($beds->count() > 0) {
          foreach ($beds as $bed)
            $bed->setBedroom(null);

          $this->em->flush();
        }
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
