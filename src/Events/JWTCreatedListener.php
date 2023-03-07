<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class JWTCreatedListener
{
  public function onJWTAuthenticated(JWTCreatedEvent $event): void
  {
    $user = $event->getUser();
    $payload = $event->getData();

    $payload['id'] = $user->getId();
    $payload['username'] = $user->getUserIdentifier();
    $payload['roles'] = $user->getRoles();
    $payload['email'] = $user->getEmail() ?? null;
    $payload['tel'] = $user->getTel();
    $payload['name'] = $user->getName() ?? null;
    $payload['agent'] = null !== $user->getAgent() ? $user->getAgent()->getId() : null;

    if ($user->isIsActive()) $event->setData($payload);
    else throw new NotFoundHttpException('Cet utilisateur a été désactivé. Veuillez contacter l\'administrateur.');
  }
}
