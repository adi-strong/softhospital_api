<?php

namespace App\Events\DrugstoreSupplyEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\BoxHistoric;
use App\Entity\DrugstoreSupply;
use App\Entity\DrugstoreSupplyMedicine;
use App\Repository\BoxRepository;
use App\Repository\MedicineRepository;
use App\Services\HandleCurrentUserService;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnPostDrugstoreSupplyEvent implements EventSubscriberInterface
{
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

  public function __construct(
    private readonly HandleCurrentUserService $user,
    private readonly MedicineRepository $repository,
    private readonly BoxRepository $boxRepository,
    private readonly EntityManagerInterface $em)
  {
  }

  /**
   * @throws Exception
   * @throws \Exception
   */
  public function handler(ViewEvent $event)
  {
    $drugstore = $event->getControllerResult();
    $method = $event->getRequest()->getMethod();
    if ($drugstore instanceof DrugstoreSupply && $method === Request::METHOD_POST) {
      $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
      $medicines = $drugstore->values;
      $released = $drugstore->getReleased() ?? new DateTime();

      $drugstore->setReleased($released);
      $drugstore->setHospital($hospital);
      $drugstore->setUser($this->user->getUser());

      foreach ($medicines as $medicine) {
        if (isset($medicine['id'])) {
          $findMedicine = $this->repository->findMedicine($medicine['id']);
          if (null !== $findMedicine) {
            $quantity = (int) $medicine['quantity'] ?? null;
            $cost = $medicine['cost'] ?? null;
            $price = $medicine['price'] ?? null;
            $expiryDate = null;
            $date = isset($medicine['expiryDate'])
              ? $expiryDate = new DateTime($medicine['expiryDate'])
              : null;

            if (null !== $cost) $findMedicine->setCost($cost);
            if (null !== $price) $findMedicine->setPrice($price);

            if (null !== $quantity) {
              $findMedicine->setQuantity($findMedicine->getQuantity() + $quantity);
              $findMedicine->setTotalQuantity($findMedicine->getQuantity());
              $findMedicine->setReleased($released);

              if (null !== $date && $released < new $expiryDate) {
                $daysRemainder = $expiryDate->diff($released)->days;
                $findMedicine->setExpiryDate($expiryDate);
                $findMedicine->setDaysRemainder($daysRemainder);
              }
              else throw new Exception("Date de péremption non valide.");

              $supply = (new DrugstoreSupplyMedicine())
                ->setQuantity($quantity)
                ->setExpiryDate($expiryDate)
                ->setMedicine($findMedicine)
                ->setDrugstoreSupply($drugstore)
                ->setCost($findMedicine->getCost());

              $drugstore->addDrugstoreSupplyMedicine($supply);
            }
            else throw new Exception('La quantité doit être renseignée.');
          }
        }
      }

      $findBox = $this->boxRepository->findBox($hospital->getId());
      if (null !== $findBox) {
        $boxHistoric = (new BoxHistoric())
          ->setBox($findBox)
          ->setCreatedAt($released)
          ->setAmount($drugstore->getAmount())
          ->setTag('output');
        $this->em->persist($boxHistoric);
      }

      $this->em->flush();

    }
  }
}
