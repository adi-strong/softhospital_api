<?php

namespace App\Events\ExamEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\ExamCategory;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnDeleteExamCategoryEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $category = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($category instanceof ExamCategory && $method === Request::METHOD_DELETE) {
      $exams = $category->getExams();
      if ($exams->count() > 0) {
        foreach ($exams as $exam)
          $category->removeExam($exam);

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
