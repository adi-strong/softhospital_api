<?php

namespace App\Events\ActEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ActCategory;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnDeleteActCategoryEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $category = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($category instanceof ActCategory && $method === Request::METHOD_DELETE) {
      $acts = $category->getActs();
      if ($acts->count() > 0) {
        foreach ($acts as $act)
          $category->removeAct($act);

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
