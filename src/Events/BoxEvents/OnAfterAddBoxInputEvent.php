<?php

namespace App\Events\BoxEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxInput;
use App\Services\BoxSumService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterAddBoxInputEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly BoxSumService $boxSumService)
  {
  }

  public function handler(ViewEvent $event)
  {
    $input = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($input instanceof BoxInput && $method === Request::METHOD_POST) {
      $box = $input->getBox();
      $sum = $this->boxSumService->getBoxSum($box);
      $box->setSum($sum);

      $this->em->flush();
    }
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::POST_WRITE,
      ]
    ];
  }
}
