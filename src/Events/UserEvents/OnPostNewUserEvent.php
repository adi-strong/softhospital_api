<?php

namespace App\Events\UserEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Agent;
use App\Entity\Hospital;
use App\Entity\User;
use App\Repository\AgentRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OnPostNewUserEvent implements EventSubscriberInterface
{
 public function __construct(
   private readonly HandleCurrentUserService    $user,
   private readonly UserPasswordHasherInterface $encoder,
   private readonly AgentRepository $agentRepository,
   private readonly EntityManagerInterface $em) { }

  public function handler(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($user instanceof User && $method === Request::METHOD_POST) {
      //$hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      if (null === $this->user->getUser()) {
        $user->setHospital(
          (new Hospital())
            ->setDenomination('Inconnue')
            ->setUser($this->user->getUser())
        );
        $user->setRoles(['ROLE_OWNER_ADMIN']);
      }
      else {
        if (null !== $user->agentId) {
          $agent = $this->agentRepository->find($user->agentId);
          if (null !== $agent) $user->setAgent($agent);

          $this->em->flush();
        }

        $user->setUser($this->user->getUser());
        $user->setUId($this->user->getUId());
        $user->setHospitalCenter($this->user->getHospital() ?? $this->user->getHospitalCenter());
      }

      $password = $this->encoder->hashPassword($user, $user->getPassword());
      $user->setPassword($password);
      $user->setCreatedAt(new DateTime('now'));
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
