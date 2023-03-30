<?php

namespace App\Services\QBStatsServices;

use App\Repository\BoxRepository;
use App\Services\HandleCurrentUserService;
use App\Services\QueryBuilderConnectionService;
use App\Services\RoundAmountService;
use Doctrine\DBAL\Exception;
use JetBrains\PhpStorm\ArrayShape;

class Stats1SectionQueryBuilderService
{
  public function __construct(
    private readonly QueryBuilderConnectionService $qbService,
    private readonly MomentStatsService $momentService,
    private readonly HandleCurrentUserService $user,
    private readonly BoxRepository $boxRepository,
    private readonly RoundAmountService $roundAmountService)
  {
  }

  // Handle get files (consultations) stats data

  /**
   * @param $hospId
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['files' => "int|mixed", 'lastFiles' => "int|mixed", 'filesStat' => "float|int|mixed"])]
  public function getFileStats($year, $month): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $getLastMonth = $this->momentService->getLastMonth($month);
    $getLastYear = $this->momentService->getLastYear($year);

    $lastMonth = $getLastMonth < 1 ? 12 : $getLastMonth;
    $lastYear = $getLastMonth <= 0 ? $getLastYear : $year;

    $countFilesQuery = $this->countNbFilesQuery($hospital->getId(), $year, $month);
    $countLastFilesQuery = $this->countNbFilesQuery($hospital->getId(), $lastYear, $lastMonth);

    $files = $countFilesQuery !== false ? $countFilesQuery[0] : 0;
    $lastFiles = $countLastFilesQuery !== false ? $countLastFilesQuery[0] : 0;
    $res1 = $files - $lastFiles;
    $filesStat = ($res1 * 100) / max($lastFiles, 1);

    return [
      'files' => $files,
      'lastFiles' => $lastFiles,
      'filesStat' => $this->roundAmountService->roundAmount($filesStat, 2),
    ];
  }

  /**
   * @param $hospId
   * @param $year
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['files' => "int|mixed", 'lastFiles' => "int|mixed", 'filesStat' => "float"])]
  public function getFileStatsByYear($year): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $lastYear = $this->momentService->getLastYear($year);

    $countFilesForCurrentYearQuery = $this->countNbFilesByYearQuery($hospital->getId(), $year);
    $countFilesForLastYearQuery = $this->countNbFilesByYearQuery($hospital->getId(), $lastYear);

    $files = $countFilesForCurrentYearQuery !== false ? $countFilesForCurrentYearQuery[0] : 0;
    $lastFiles = $countFilesForLastYearQuery !== false ? $countFilesForLastYearQuery[0] : 0;
    $res1 = $files - $lastFiles;
    $filesStat = ($res1 * 100) / max($lastFiles, 1);

    return [
      'files' => $files,
      'lastFiles' => $lastFiles,
      'filesStat' => $this->roundAmountService->roundAmount($filesStat, 2),
    ];
  }

  /**
   * @param $hospId
   * @param $year
   * @return array|bool
   * @throws Exception
   */
  private function countNbFilesByYearQuery($hospId, $year): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT COUNT(c.id) count
        FROM consultation c 
          LEFT JOIN hospital h ON h.id = c.hospital_id
        WHERE c.hospital_id IS NOT NULL 
          AND h.id = :hospitalId
          AND date_format(c.created_at, '%Y') = :format
          GROUP BY h.id
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospitalId' => $hospId,
      'format' => $year,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $hospId
   * @param $year
   * @param $month
   * @return array|bool
   * @throws Exception
   */
  private function countNbFilesQuery($hospId, $year, $month): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT COUNT(c.id) count
        FROM consultation c 
          LEFT JOIN hospital h ON h.id = c.hospital_id
        WHERE c.hospital_id IS NOT NULL 
          AND h.id = :hospitalId
          AND date_format(c.created_at, '%Y-%m') = :format
          GROUP BY h.id
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospitalId' => $hospId,
      'format' => $year.'-'.$month
    ]);

    return $resSet->fetchNumeric();
  }

  // End handle get files (consultations) stats data

  /*
   * **********************************************************************************************************
   * **********************************************************************************************************
   */

  // Handle get get revenue (input box) stats data

  /**
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['sum' => "int|mixed", 'lasSum' => "int|mixed", 'revenueStat' => "float"])]
  public function getRevenueStats($year, $month): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $box = $this->boxRepository->findBox($hospital->getId());

    $getLastMonth = $this->momentService->getLastMonth($month);
    $getLastYear = $this->momentService->getLastYear($year);

    $lastMonth = $getLastMonth < 1 ? 12 : $getLastMonth;
    $lastYear = $getLastMonth <= 0 ? $getLastYear : $year;

    $getSumQuery = false;
    $getLastSumQuery = false;
    if (null !== $box) {
      $getSumQuery = $this->getRevenueSumsQuery($box?->getId(), $year, $month);
      $getLastSumQuery = $this->getRevenueSumsQuery($box?->getId(), $lastYear, $lastMonth);
    }

    $sum = $getSumQuery !== false ? $getSumQuery[0] : 0;
    $lastSum = $getLastSumQuery !== false ? $getLastSumQuery[0] : 0;
    $res = $sum - $lastSum;
    $revenueStat = ($res * 100) / max($lastSum, 1);

    return [
      'sum' => $sum,
      'lasSum' => $lastSum,
      'revenueStat' => $this->roundAmountService->roundAmount($revenueStat, 2),
    ];
  }

  /**
   * @param $year
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['sum' => "int|mixed", 'lasSum' => "int|mixed", 'revenueStat' => "float"])]
  public function getRevenueByYearStats($year): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $box = $this->boxRepository->findBox($hospital->getId());
    $getLastYear = $this->momentService->getLastYear($year);

    $getSumQuery = false;
    $getLastSumQuery = false;

    if (null !== $box) {
      $getSumQuery = $this->getRevenueSumsByYearQuery($box->getId(), $year);
      $getLastSumQuery = $this->getRevenueSumsByYearQuery($box->getId(), $getLastYear);
    }

    $sum = $getSumQuery !== false ? $getSumQuery[0] : 0;
    $lastSum = $getLastSumQuery !== false ? $getLastSumQuery[0] : 0;
    $res = $sum - $lastSum;
    $revenueStat = ($res * 100) / max($lastSum, 1);

    return [
      'sum' => $sum,
      'lasSum' => $lastSum,
      'revenueStat' => $this->roundAmountService->roundAmount($revenueStat, 2),
    ];
  }

  /**
   * @param $boxId
   * @param $year
   * @return array|bool
   * @throws Exception
   */
  private function getRevenueSumsByYearQuery($boxId, $year): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) sum
      FROM box_historic bh
       LEFT JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND bh.tag = 'input'
       AND date_format(bh.created_at, '%Y') = :format
       GROUP by b.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $boxId
   * @param $year
   * @param $month
   * @return array|bool
   * @throws Exception
   */
  private function getRevenueSumsQuery($boxId, $year, $month): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) sum
      FROM box_historic bh
       LEFT JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND bh.tag = 'input'
       AND date_format(bh.created_at, '%Y-%m') = :format
       GROUP by b.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year.'-'.$month
    ]);

    return $resSet->fetchNumeric();
  }

  // End handle get get revenue (input box) stats data

  /*
   * **********************************************************************************************************
   * **********************************************************************************************************
   */

  // Handle get patients stats

  /**
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['patients' => "int|mixed", 'lastPatients' => "int|mixed", 'patientsStat' => "float"])]
  public function getPatientsStats($year, $month): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $getLastMonth = $this->momentService->getLastMonth($month);
    $getLastYear = $this->momentService->getLastYear($year);

    $lastMonth = $getLastMonth < 1 ? 12 : $getLastMonth;
    $lastYear = $getLastMonth <= 0 ? $getLastYear : $year;

    $countPatientsQuery = $this->countPatientsQuery($hospital->getId(), $year, $month);
    $countLastPatientsQuery = $this->countPatientsQuery($hospital->getId(), $lastYear, $lastMonth);

    $patients = $countPatientsQuery !== false ? $countPatientsQuery[0] : 0;
    $lastPatients = $countLastPatientsQuery !== false ? $countLastPatientsQuery[0] : 0;
    $res1 = $patients - $lastPatients;
    $patientsStat = ($res1 * 100) / max($lastPatients, 1);

    return [
      'patients' => $patients,
      'lastPatients' => $lastPatients,
      'patientsStat' => $this->roundAmountService->roundAmount($patientsStat, 2),
    ];
  }

  /**
   * @param $year
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['patients' => "int|mixed", 'lastPatients' => "int|mixed", 'patientsStat' => "float"])]
  public function getPatientsByYearStats($year): array
  {
    $hospital = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $lastYear = $this->momentService->getLastYear($year);

    $countPatientsForCurrentYearQuery = $this->countPatientsByYearQuery($hospital->getId(), $year);
    $countPatientsForLastYearQuery = $this->countPatientsByYearQuery($hospital->getId(), $lastYear);

    $patients = $countPatientsForCurrentYearQuery !== false ? $countPatientsForCurrentYearQuery[0] : 0;
    $lastPatients = $countPatientsForLastYearQuery !== false ? $countPatientsForLastYearQuery[0] : 0;
    $res1 = $patients - $lastPatients;
    $patientsStat = ($res1 * 100) / max($lastPatients, 1);

    return [
      'patients' => $patients,
      'lastPatients' => $lastPatients,
      'patientsStat' => $this->roundAmountService->roundAmount($patientsStat, 2),
    ];
  }

  /**
   * @param $hospId
   * @param $year
   * @return array|bool
   * @throws Exception
   */
  private function countPatientsByYearQuery($hospId, $year): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       COUNT(p.id) AS count
      FROM patient p
       LEFT JOIN hospital h ON h.id = p.hospital_id
      WHERE h.id = :hospId
       AND p.hospital_id IS NOT NULL
       AND date_format(p.created_at, '%Y') = :format
      GROUP BY h.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $hospId
   * @param $year
   * @param $month
   * @return array|bool
   * @throws Exception
   */
  private function countPatientsQuery($hospId, $year, $month): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       COUNT(p.id) AS count
      FROM patient p
       LEFT JOIN hospital h ON h.id = p.hospital_id
      WHERE h.id = :hospId
       AND p.hospital_id IS NOT NULL
       AND date_format(p.created_at, '%Y-%m') = :format
      GROUP BY h.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month
    ]);

    return $resSet->fetchNumeric();
  }

  // End handle get patients stats
}
