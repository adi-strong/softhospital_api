<?php

namespace App\Controller\StatControllers;

use App\Services\QBStatsServices\Stats2SectionQueryBuilderService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class Stats2SectionController extends AbstractController
{
  public function __construct(private readonly Stats2SectionQueryBuilderService $builder) { }

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_box_by_year/{year}', name: 'get_box_by_year')]
  public function getBoxByYear($year): JsonResponse
  {
    $stats = $this->builder->getBoxByYear($year);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_box_by_month/{year}/{month}', name: 'get_box_by_month')]
  public function getBoxByMonth($year, $month): JsonResponse
  {
    $stats = $this->builder->getBoxByMonth($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_stats2_by_month/{year}/{month}', name: 'get_stats2_by_month')]
  public function getStats2ByMonthAction($year, $month): JsonResponse
  {
    $stats = $this->builder->getStats2Data($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_stats2_by_year/{year}', name: 'get_stats2_by_year')]
  public function getStats2ByYearAction($year): JsonResponse
  {
    $stats = $this->builder->getStats2ByYearData($year);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_most_sales_drugs/{year}/{month}', name: 'get_most_sales_drugs')]
  public function getMostSalesDrugsAction($year, $month): JsonResponse
  {
    $stats = $this->builder->getMostSalesDrugsData($year, $month);
    return new JsonResponse($stats);
  }
}
