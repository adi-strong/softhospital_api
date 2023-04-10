<?php

namespace App\Events\PatientEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Activities;
use App\Entity\Agent;
use App\Entity\Patient;
use App\Services\HandleCurrentUserService;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnAddNewPatientEvent implements EventSubscriberInterface
{
  public function __construct(private readonly HandleCurrentUserService $user, private readonly EntityManagerInterface $em)
  {
  }

  public function handler(ViewEvent $event)
  {
    $patient = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();

    if ($patient instanceof Patient && $method === Request::METHOD_POST) {
      $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();

      $patient->setHospital($this->user->getHospital() ?? $this->user->getHospitalCenter());
      $patient->setUser($this->user->getUser());
      $patient->setCreatedAt(new DateTime('now'));
      $patient->setHospital($hosp);

      $lastName = $patient?->getLastName();
      $firstName = $patient?->getFirstName();
      $fullName = $patient->getName().' '.$lastName.' '.$firstName;
      $patient->setFullName(trim($fullName, ' '));

      $slug = (new Slugify())->slugify($fullName);
      $activity = (new Activities())
        ->setCreatedAt(new DateTime())
        ->setTitle('Enregistrement d\'un patient')
        ->setHospital($hosp)
        ->setAuthor($this->user->getUser())
        ->setDescription('Ajout d\'un nouveau patient : '.$patient->getName())
        ->setSlug('/'.$slug);

      $this->em->persist($activity);
      $this->em->flush();
    }
  }

  #[ArrayShape([KernelEvents::VIEW => "array"])]
  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::VIEW => [
        'handler',
        EventPriorities::PRE_VALIDATE,
      ]
    ];
  }
}
