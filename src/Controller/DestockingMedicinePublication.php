<?php

namespace App\Controller;

use App\Controller\handler\DestockingMedicineHandler;
use App\Entity\Medicine;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class DestockingMedicinePublication extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly HandleCurrentUserService $user,
    private readonly DestockingMedicineHandler $handler) { }

  #[Route('/api/medicines/{id}/destocking_publication', name: 'post_destocking_publication', methods: ['POST'])]
  public function postDestockingMedicine($id): JsonResponse
  {
    $repository = $this->em->getRepository(Medicine::class);
    $medicine = $repository->find($id);

    if (!$medicine) throw $this->createNotFoundException("Aucun remède d'id ".$id." touvé.");

    $this->handler->handle($medicine, $this->user->getUser(), $this->em);

    $this->em->flush();
    $data = [
      'quantity' => $medicine->getQuantity(),
      'price' => $medicine->getPrice(),
      'cost' => $medicine->getCost(),
      'user' => $medicine->getUser(),
      'id' => $medicine->getId(),
      'totalQuantity' => $medicine->getTotalQuantity(),
      'createdAt' => $medicine->getCreatedAt(),
      'vTA' => $medicine->getVTA(),
      'wording' => $medicine->getWording(),
      'daysRemainder' => $medicine->getDaysRemainder(),
      'expiryDate' => $medicine->getExpiryDate(),
      'released' => $medicine->getReleased(),
      'category' => $medicine->getCategory(),
      'code' => $medicine->getCode(),
      'consumptionUnit' => $medicine->getConsumptionUnit(),
      'subCategory' => $medicine->getSubCategory(),
    ];

    return new JsonResponse($data);
  }
}
