<?php

namespace App\Events\ParametersEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Parameters;
use App\Repository\ParametersRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewParameterEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly EntityManagerInterface   $em,
    private readonly ParametersRepository     $repository) { }

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    $parameters = $event->getControllerResult();
    if ($parameters instanceof Parameters && $method === Request::METHOD_POST) {
      if ($this->user->getUser() !== null && $parameters->isUpdated) {
        try {
          $oldParams = $this
            ->repository
            ->findLastParameters($this->user->getHospital() ?? $this->user->getHospitalCenter());
          if ($oldParams instanceof Parameters) {
            $oldParams->setUpdatedAt(new DateTime());
          }
        } catch (NonUniqueResultException $e) { }
      } // Are lasts parameters exists ???

      if (null !== $this->user->getUser() &&
        ($this->user->getHospital() !== null || $this->user->getHospitalCenter() !== null)) {
        $parameters->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      }

      if ($parameters->getRate() === 0) $parameters->setRate(null);

      $parameters->setCreatedAt(new DateTime());
      $parameters->setUpdatedAt(null);

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
