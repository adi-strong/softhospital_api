<?php

namespace App\Events\AgentEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Agent;
use App\Services\HandleCurrentUserService;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewAgentEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user)
  {
  }

  public function handler(ViewEvent $event)
  {
    $agent = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($agent instanceof Agent && $method === Request::METHOD_POST) {
      if ($this->user->getUser() !== null) {
        $lastName = $agent?->getLastName();
        $firstName = $agent?->getFirstName();
        $agent->setUser($this->user->getUser());
        $agent->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());

        $fullName = $agent->getName().' '.$lastName.' '.$firstName;
        $agent->setFullName(trim($fullName, ' '));
      }

      $agent->setCreatedAt(new DateTime('now'));
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
