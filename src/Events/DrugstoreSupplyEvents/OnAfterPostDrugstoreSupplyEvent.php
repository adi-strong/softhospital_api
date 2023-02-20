<?php

namespace App\Events\DrugstoreSupplyEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\DrugstoreSupply;
use App\Repository\BoxRepository;
use App\Services\BoxSumService;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAfterPostDrugstoreSupplyEvent implements EventSubscriberInterface
{
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

  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly BoxRepository $boxRepository,
    private readonly BoxSumService $boxSumService,
    private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $drugstore = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($drugstore instanceof DrugstoreSupply && $method === Request::METHOD_POST) {
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();

      if ($this->user->getUser() !== null) {
        $findBox = $this->boxRepository->findBox($hospital->getId());
        if (null !== $findBox) {
          $findBox->setOutputSum($this->boxSumService->getBoxOutputSum($findBox));
          $this->em->flush();
        }
      }
    }
  }
}
