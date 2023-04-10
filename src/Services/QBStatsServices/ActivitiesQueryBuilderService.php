<?php

namespace App\Services\QBStatsServices;

use App\Services\HandleCurrentUserService;
use App\Services\QueryBuilderConnectionService;
use Doctrine\DBAL\Exception;

class ActivitiesQueryBuilderService
{
  public function __construct(
    private readonly QueryBuilderConnectionService $qbService,
    private readonly MomentStatsService $momentService,
    private readonly HandleCurrentUserService $user)
  {
  }

  /**
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  public function getLastsActivitiesByLastMonth($year, $month): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $getLastMonth = $this->momentService->getLastMonth($month);
    $getLastYear = $this->momentService->getLastYear($year);
    $lastMonth = $getLastMonth < 1 ? 12 : $getLastMonth;
    $lastYear = $getLastMonth <= 0 ? $getLastYear : $year;

    return $this->lastsActivitiesByMonthQuery($lastYear, $lastMonth, $hosp->getId());
  }

  /**
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  public function getLastsActivitiesByMonth($year, $month): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $getMonth = $month < 1 ? 12 : $month;
    $getYear = $month < 1 ? ($year - 1) : $year;

    return $this->lastsActivitiesByMonthQuery($getYear, $getMonth, $hosp->getId());
  }

  /**
   * @param $year
   * @return array
   * @throws Exception
   */
  public function getLastsActivitiesByYear($year): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    return $this->lastsActivitiesByYear($year, $hosp->getId());
  }


  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @return array
   * @throws Exception
   */
  private function lastsActivitiesByMonthQuery($year, $month, $hospId): array
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       a.id 'id',
       a.title 'title',
       a.description 'description',
       a.created_at 'createdAt',
       u.username 'username',
       u.name 'name'
      FROM
      activities a
       JOIN hospital h ON h.id = a.hospital_id
       LEFT JOIN user u ON u.id = a.author_id
      WHERE h.id = :hospId
       AND date_format(a.created_at, '%Y-%m') = :format
      ORDER BY a.created_at DESC
      LIMIT 0,6;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchAllAssociative();
  }

  /**
   * @param $year
   * @param $hospId
   * @return array
   * @throws Exception
   */
  private function lastsActivitiesByYear($year, $hospId): array
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       a.id 'id',
       a.title 'title',
       a.description 'description',
       a.created_at 'createdAt',
       u.username 'username',
       u.name 'name'
      FROM
      activities a
       JOIN hospital h ON h.id = a.hospital_id
       LEFT JOIN user u ON u.id = a.author_id
      WHERE h.id = :hospId
       AND date_format(a.created_at, '%Y') = :format
      ORDER BY a.created_at DESC
      LIMIT 0,6;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year,
    ]);

    return $resSet->fetchAllAssociative();
  }
}
