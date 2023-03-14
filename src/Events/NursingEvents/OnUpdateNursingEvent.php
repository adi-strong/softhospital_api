<?php

namespace App\Events\NursingEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Nursing;
use App\Entity\NursingTreatment;
use App\Entity\TreatmentInvoiceBasket;
use App\Repository\NursingTreatmentRepository;
use App\Repository\TreatmentRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnUpdateNursingEvent implements EventSubscriberInterface
{
  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly NursingTreatmentRepository $repository,
    private readonly TreatmentRepository $treatmentRepository,
    private readonly EntityManagerInterface $em)
  {
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

  public function handler(ViewEvent $event)
  {
    $method = $event->getRequest()->getMethod();
    $nursing = $event->getControllerResult();
    if ($nursing instanceof Nursing && $method === Request::METHOD_PATCH) {
      $createdAt = new DateTime();
      $values = $nursing?->treatments;
      $treatments = $nursing->getNursingTreatments();
      $consultation = $nursing->getConsultation();
      $invoice = $consultation->getInvoice();
      $invoiceAmount = $invoice->getAmount();

      if ($nursing->isIsCompleted() === false) {
        if ($nursing->isIsPublished() === true && null !== $values) {
          if ($nursing->isNursingCompleted === true) $nursing->setIsCompleted(true);
          foreach ($values as $value) {
            $id = $value['id'] ?? 0;
            $findOne = $this->treatmentRepository->find($id);
            if (null !== $findOne) {
              $newTreatment = (new NursingTreatment())
                ->setTreatment($findOne)
                ->setNursing($nursing)
                ->setUser($this->user->getUser())
                ->setCreatedAt($createdAt);

              $treatmentInvoiceBasket = (new TreatmentInvoiceBasket())
                ->setTreatment($findOne)
                ->setInvoice($invoice)
                ->setPrice($findOne->getPrice());

              $invoiceAmount += $findOne->getPrice();
              $this->em->persist($treatmentInvoiceBasket);

              $nursing->addNursingTreatment($newTreatment);
            }
          }
        }
        elseif (null !== $values) {
          foreach ($treatments as $treatment) {
            $findOne = $this->repository->findNursingTreatment($treatment->getTreatment(), $nursing);
            if (null !== $findOne) {
              foreach ($values as $value) {
                $id = $value['id'] ?? 0;
                if ($treatment->getTreatment()->getId() === $id) {
                  $nursing->setIsPublished(true);
                  $findOne->setUser($this->user->getUser());
                  $findOne->setCreatedAt($createdAt);
                }
              }
            }
          }
        }

        $this->em->flush();
      }

    }
  }
}
