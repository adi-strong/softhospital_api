<?php

namespace App\Events\ExamEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Exam;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewExamEvents implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $exam = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($exam instanceof Exam && $method === Request::METHOD_POST) {
      $exam->setCreatedAt(new DateTime());
      $exam->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
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
