<?php

namespace App\Events\BoxEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxExpense;
use App\Entity\BoxHistoric;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddBoxExpenseEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $expense = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($expense instanceof BoxExpense && $method === Request::METHOD_POST) {
      $createdAt = new DateTime('now');

      $expense->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $expense->setUser($this->user->getUser());
      $expense->setCreatedAt($createdAt);

      $boxHistoric = (new BoxHistoric())
        ->setBox($expense->getBox())
        ->setCreatedAt($createdAt)
        ->setAmount($expense->getAmount())
        ->setTag('output');

      $this->em->persist($boxHistoric);
      $this->em->flush();
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
