<?php

namespace App\Events\UserEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Hospital;
use App\Entity\User;
use App\Services\HandleCurrentUserService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class OnPostNewUserEvent implements EventSubscriberInterface
{
 public function __construct(
   private readonly HandleCurrentUserService $currentUser,
   private readonly UserPasswordHasherInterface $encoder) { }

  public function handler(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($user instanceof User && $method === Request::METHOD_POST) {
      if (null === $this->currentUser->getUser()) {
        $user->setHospital(
          (new Hospital())
            ->setDenomination('Inconnue')
            ->setUser($this->currentUser->getUser())
        );
        $user->setRoles(['ROLE_OWNER_ADMIN']);
      }
      else {
        $user->setUser($this->currentUser->getUser());
        $user->setUId($this->currentUser->getUId());
        $user->setHospitalCenter($this->currentUser->getHospital());
      }

      $password = $this->encoder->hashPassword($user, $user->getPassword());
      $user->setPassword($password);
      $user->setCreatedAt(new \DateTime('now'));
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
