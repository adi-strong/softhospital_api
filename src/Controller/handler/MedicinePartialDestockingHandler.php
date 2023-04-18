<?php

namespace App\Controller\handler;

use App\Entity\DestockMedicineForHospital;
use App\Entity\Medicine;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class MedicinePartialDestockingHandler
{
  public function handle(Medicine $medicine, RequestStack $requestStack, EntityManagerInterface $em, UserInterface $user): void
  {
    $request = $requestStack->getCurrentRequest();
    $data = json_decode($request->getContent(), true);
    $dStockQty = $data['dStockQuantity'] ?? null;
    if ($dStockQty !== null) {
      $quantity = $medicine->getQuantity() - $dStockQty;
      $destockingForHospital = (new DestockMedicineForHospital())
        ->setQuantity($dStockQty)
        ->setCreatedAt(new DateTime())
        ->setMedicine($medicine)
        ->setUser($user);
      $em->persist($destockingForHospital);

      $medicine->setQuantity($quantity);
    }
  }
}
