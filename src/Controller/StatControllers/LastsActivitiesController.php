<?php

namespace App\Controller\StatControllers;

use App\Services\QBStatsServices\ActivitiesQueryBuilderService;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class LastsActivitiesController extends AbstractController
{
  public function __construct(private readonly ActivitiesQueryBuilderService $builderService)
  {
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_lasts_activities_by_month/{year}/{month}', name: 'get_lasts_activities_by_month')]
  public function getLastsActivitiesByMonthAction($year, $month): JsonResponse
  {
    $stats = $this->builderService->getLastsActivitiesByMonth($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @param $month
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_lasts_activities_by_last_month/{year}/{month}', name: 'get_lasts_activities_by_last_month')]
  public function getLastsActivitiesByLastMonthAction($year, $month): JsonResponse
  {
    $stats = $this->builderService->getLastsActivitiesByLastMonth($year, $month);
    return new JsonResponse($stats);
  }

  /**
   * @param $year
   * @return JsonResponse
   * @throws Exception
   */
  #[Route('/api/get_lasts_activities_by_year/{year}', name: 'get_lasts_activities_by_year')]
  public function getLastsActivitiesByYearAction($year): JsonResponse
  {
    $stats = $this->builderService->getLastsActivitiesByYear($year);
    return new JsonResponse($stats);
  }
}
