<?php

namespace App\Events\BoxEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Activities;
use App\Entity\BoxHistoric;
use App\Entity\BoxInput;
use App\Repository\ParametersRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddBoxInputEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly ParametersRepository $parametersRepository,
    private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $input = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($input instanceof BoxInput && $method === Request::METHOD_POST) {
      $createdAt = new DateTime();
      $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $parameter = $this->parametersRepository->findLastParameter($hosp);

      $activity = (new Activities())
        ->setTitle('Entrée en caisse d\'un montant de : ')
        ->setDescription("Entrée en caisse d'une somme de : ".$parameter[0] ?? '')
        ->setHospital($hosp)
        ->setCreatedAt($createdAt)
        ->setAuthor($this->user->getUser());
      $this->em->persist($activity);

      $input->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $input->setUser($this->user->getUser());
      $input->setCreatedAt($createdAt);

      $boxHistoric = (new BoxHistoric())
        ->setBox($input->getBox())
        ->setCreatedAt($createdAt)
        ->setAmount($input->getAmount())
        ->setTag('input');

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
