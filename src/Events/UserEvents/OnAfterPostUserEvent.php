<?php

namespace App\Events\UserEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Box;
use App\Entity\Parameters;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterPostUserEvent implements EventSubscriberInterface
{
  public function __construct(private readonly EntityManagerInterface $em) { }

  public function handler(ViewEvent $event)
  {
    $user = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($user instanceof User && $method === Request::METHOD_POST) {
      if (null === $user->getUser()) {
        $hospital = $user->getHospital() ?? $user->getHospitalCenter();
        $user->setHospitalCenter($hospital);
        $user->setUId(uniqid().$user->getId());

        $box = (new Box())
          ->setHospital($hospital)
          ->setSum(0)
          ->setOutputSum(0);

        $parameters = (new Parameters())
          ->setCurrency('$')
          ->setName("(US) United States of America ' $ '")
          ->setCode('US')
          ->setHospital($hospital)
          ->setCreatedAt(new DateTime());

        $this->em->persist($box);
        $this->em->persist($parameters);
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
        EventPriorities::POST_WRITE,
      ]
    ];
  }
}
