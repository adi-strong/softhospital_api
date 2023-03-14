<?php

namespace App\Events\AgentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Agent;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateAgentEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
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

  public function handler(ViewEvent $event)
  {
    $agent = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($agent instanceof Agent && $method === Request::METHOD_PATCH) {
      $userAccount = $agent?->getUserAccount();
      $lastName = $agent?->getLastName();
      $firstName = $agent?->getFirstName();
      $fullName = $agent->getName().' '.$lastName.' '.$firstName;
      $agent->setFullName(trim($fullName, ' '));

      $name = $firstName.' '.$agent->getName();
      $userAccount?->setName(trim($name, ' '));

      $this->em->flush();
    }
  }
}
