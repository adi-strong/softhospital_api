<?php

namespace App\Events\DrugstoreSupplyEvents;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\DrugstoreSupply;
use App\Entity\DrugstoreSupplyMedicine;
use App\Repository\MedicineRepository;
use App\Repository\ParametersRepository;
use App\Services\HandleCurrentUserService;
use App\Services\RoundAmountService;
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
    private readonly RoundAmountService $amountService,
    private readonly ParametersRepository $parametersRepository,
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
            $cost = $medicine['cost'] ?? $findMedicine->getCost();
            $price = $medicine['price'] ?? $findMedicine->getPrice();
            $vTA = $medicine['vTA'] ?? null;
            $quantityLabel = $medicine['quantityLabel'] ?? null;
            $otherQty = $medicine['otherQty'] ?? 0;
            $expiryDate = null;
            $date = isset($medicine['expiryDate'])
              ? $expiryDate = new DateTime($medicine['expiryDate'])
              : null;

            // converter
            $converterCost = $cost;
            $parameter = $this->parametersRepository->findParameters($hospital);
            if (null !== $parameter) {
              $rate = $parameter->getRate() ?? null;
              $fOperation = $parameter->getFOperation();
              $currency = $parameter->getCurrency() ?? null;
              if ($currency !== null) {
                if (null !== $rate && $fOperation !== null && $currency !== $drugstore->getCurrency()) {
                  if ($fOperation === '*') $converterCost = max(($cost * $rate), 1);
                  elseif ($fOperation === '/') $converterCost = max(($cost / $rate), 1);
                }
              }
            }
            // end converter

            $unitCost = $otherQty > 0 ? $converterCost / $otherQty : $converterCost;
            $unitPrice = $vTA !== null ? (($unitCost * $vTA) / 100) + $unitCost : $price;

            $findMedicine->setCost($this->amountService->roundAmount($unitCost, 2));
            $findMedicine->setPrice($this->amountService->roundAmount($unitPrice, 2));

            if (null !== $quantity) {
              $newQty = $findMedicine->getQuantity() + $quantity;
              $findMedicine->setQuantity($newQty);
              $findMedicine->setTotalQuantity($newQty);
              $findMedicine->setVTA($vTA);
              $findMedicine->setReleased($released);

              if (null !== $date && $released < new $expiryDate) {
                $daysRemainder = $expiryDate->diff($released)->days;
                $findMedicine->setExpiryDate($expiryDate);
                $findMedicine->setDaysRemainder($daysRemainder);
              }
              else throw new Exception("Date de péremption non valide.");

              $supply = (new DrugstoreSupplyMedicine())
                ->setOtherQty($otherQty)
                ->setQuantityLabel($quantityLabel ?? 'Pièce')
                ->setQuantity($quantity)
                ->setExpiryDate($expiryDate)
                ->setMedicine($findMedicine)
                ->setDrugstoreSupply($drugstore)
                ->setVTA($vTA)
                ->setCost($cost);

              $drugstore->addDrugstoreSupplyMedicine($supply);
            }
            else throw new Exception('La quantité doit être renseignée.');
          }
        }
      }

      $this->em->flush();
    }
  }
}
