<?php

namespace App\Events\BoxEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Activities;
use App\Entity\BoxHistoric;
use App\Entity\BoxOutput;
use App\Repository\ParametersRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddBoxOutputEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly ParametersRepository $parametersRepository,
    private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $output = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($output instanceof BoxOutput && $method === Request::METHOD_POST) {
      $createdAt = new DateTime();
      $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $parameter = $this->parametersRepository->findLastParameter($hosp);

      $activity = (new Activities())
        ->setAuthor($this->user->getUser())
        ->setCreatedAt($createdAt)
        ->setHospital($hosp)
        ->setDescription("Dépense d'une somme de : ".$output->getAmount().' '.$parameter[0] ?? '')
        ->setTitle('Sortie en caisse');
      $this->em->persist($activity);

      $output->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $output->setCreatedAt($createdAt);
      $output->setUser($this->user->getUser());

      $boxHistoric = (new BoxHistoric())
        ->setBox($output->getBox())
        ->setCreatedAt($createdAt)
        ->setAmount($output->getAmount())
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
