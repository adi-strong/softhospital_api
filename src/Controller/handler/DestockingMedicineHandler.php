<?php

namespace App\Controller\handler;

use App\Entity\DestockingOfMedicines;
use App\Entity\Medicine;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DestockingMedicineHandler
{
  public function handle(Medicine $medicine, UserInterface $user, EntityManagerInterface $em): void
  {
    $cost = $medicine->getCost();
    $price = $medicine->getPrice();
    $quantity = $medicine->getQuantity();

    $loss = ($cost - $price) * $quantity;
    $destocking = (new DestockingOfMedicines())
      ->setMedicine($medicine)
      ->setCreatedAt(new DateTime())
      ->setUser($user)
      ->setQuantity($quantity)
      ->setCost($cost)
      ->setPrice($price)
      ->setLoss($loss);
    $em->persist($destocking);

    $medicine->setQuantity(0);
    $medicine->setTotalQuantity(0);
    $medicine->setExpiryDate(null);
  }
}
