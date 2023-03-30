<?php

namespace App\Controller\StatControllers;

use App\Services\QBStatsServices\Stats1SectionQueryBuilderService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class Stats1SectionController extends AbstractController
{
  public function __construct(private readonly Stats1SectionQueryBuilderService $statQueryBuilderService)
  {
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_file_stats/{year}/{month}', name: 'get_file_stats')]
  public function getFileStatsAction($year, $month): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getFileStats($year, $month);
    return new JsonResponse($stats);
  }
  // stats pour récupérer les fiches des (tous) les patients reçu

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_file_stats_by_year/{year}', name: 'get_file_stats_by_year')]
  public function getFileStatsByYearAction($year): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getFileStatsByYear($year);
    return new JsonResponse($stats);
  }
  // get file stats by year. where year is an option.

  // **********************************************************************************************
  // **********************************************************************************************

  // get revenue stats
  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_revenue_stats/{year}/{month}', name: 'get_revenue_stats')]
  public function getRevenueStatsAction($year, $month): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getRevenueStats($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_revenue_by_year_stats/{year}', name: 'get_revenue_by_year_stats')]
  public function getRevenueByYearStatsAction($year): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getRevenueByYearStats($year);
    return new JsonResponse($stats);
  }
  // end get revenue stats

  // **********************************************************************************************
  // **********************************************************************************************

  // get patients stats

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_patients_stats/{year}/{month}', name: 'get_patients_stats')]
  public function getPatientsStatsAction($year, $month): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getPatientsStats($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_patients_by_year_stats/{year}', name: 'get_patients_by_year_stats')]
  public function getPatientsByYearStatsAction($year): JsonResponse
  {
    $stats = $this->statQueryBuilderService->getPatientsByYearStats($year);
    return new JsonResponse($stats);
  }

  // end get patients stats
}
