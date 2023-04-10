<?php

namespace App\Controller;

use App\Entity\Covenant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class CreateCustomCovenantContractPublication extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly CreateNewCovenantContractHandler $handler)
  {
  }

  #[Route('/api/covenants/{id}/new_contract', name: 'new_contract', requirements: ['id' => '\d+'], methods: ['POST'])]
  public function onPostNewCovenantContract(Request $request, $id): JsonResponse
  {
    $repository = $this->em->getRepository(Covenant::class);
    $covenant = $repository->find($id);

    if (!$covenant) throw $this->createNotFoundException("La convention qui aurait pour id ".$id." n'est pas trouvée.");

    $this->handler->handle($request, $covenant, $this->em);
    $this->em->flush();

    $data = [
      'id' => $covenant->getId(),
      'denomination' => $covenant->getDenomination(),
      'unitName' => $covenant->getUnitName(),
      'focal' => $covenant->getFocal(),
      'tel' => $covenant->getTel(),
      'email' => $covenant->getEmail(),
      'address' => $covenant->getAddress(),
      'filePath' => $covenant->filePath,
      'createdAt' => $covenant->getCreatedAt(),
    ];
    return new JsonResponse($data);
  }
}
