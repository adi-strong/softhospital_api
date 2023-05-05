<?php

namespace App\Controller;

use App\Services\QBStatsServices\CovenantInvoicesQueryBuilderService;
use Doctrine\DBAL\Exception;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class CovenantInvoicesController extends AbstractController
{
  public function __construct(private readonly CovenantInvoicesQueryBuilderService $builder)
  {
  }

  /**
   * @param $year
   * @param $month
   * @param $covenantId
   * @return JsonResponse
   * @throws Exception
   */
  #[NoReturn] #[Route('/api/get_covenant_invoices/{year}/{month}/{covenantId}', name: 'get_covenant_invoices')]
  public function getCovenantInvoicesAction($year, $month, $covenantId): JsonResponse
  {
    $invoices = $this->builder->getInvoicesData($year, $month, $covenantId);
    return new JsonResponse($invoices);
  }
}
