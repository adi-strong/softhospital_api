<?php

namespace App\Services\QBStatsServices;

use App\Repository\BoxRepository;
use App\Services\HandleCurrentUserService;
use App\Services\QueryBuilderConnectionService;
use App\Services\RoundAmountService;
use Doctrine\DBAL\Exception;
use JetBrains\PhpStorm\ArrayShape;

class Stats2SectionQueryBuilderService
{
  public function __construct(
    private readonly QueryBuilderConnectionService $qbService,
    private readonly HandleCurrentUserService $user,
    private readonly RoundAmountService $amountService,
    private readonly BoxRepository $boxRepository)
  {
  }

  /**
   * @param $year
   * @param $month
   * @return array[]
   * @throws Exception
   */
  public function getStats2Data($year, $month): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $box = $this->boxRepository->findBox($hosp->getId());

    $countW1FilesQuery = $this->getFilesByMonthQuery($year, $month, '01', '05', $hosp->getId());
    $countW2FilesQuery = $this->getFilesByMonthQuery($year, $month, '06', '15', $hosp->getId());
    $countW3FilesQuery = $this->getFilesByMonthQuery($year, $month, '16', '21', $hosp->getId());
    $countW4FilesQuery = $this->getFilesByMonthQuery($year, $month, '22', '31', $hosp->getId());

    $filesWeek1 = $countW1FilesQuery !== false ? $countW1FilesQuery[0] : 0;
    $filesWeek2 = $countW2FilesQuery !== false ? $countW2FilesQuery[0] : 0;
    $filesWeek3 = $countW3FilesQuery !== false ? $countW3FilesQuery[0] : 0;
    $filesWeek4 = $countW4FilesQuery !== false ? $countW4FilesQuery[0] : 0;

    $revenueW1Query = 0;
    $revenueW2Query = 0;
    $revenueW3Query = 0;
    $revenueW4Query = 0;

    $revenueWeek1 = $revenueW1Query;
    $revenueWeek2 = $revenueW2Query;
    $revenueWeek3 = $revenueW3Query;
    $revenueWeek4 = $revenueW4Query;
    if (null !== $box) {
      $revenueW1Query = $this->getRevenueByMonthQuery($year, $month, '01', '05', $box->getId());
      $revenueW2Query = $this->getRevenueByMonthQuery($year, $month, '06', '15', $box->getId());
      $revenueW3Query = $this->getRevenueByMonthQuery($year, $month, '16', '21', $box->getId());
      $revenueW4Query = $this->getRevenueByMonthQuery($year, $month, '22', '31', $box->getId());

      $revenueWeek1 = $revenueW1Query !== false ? $revenueW1Query[0] : 0;
      $revenueWeek2 = $revenueW2Query !== false ? $revenueW2Query[0] : 0;
      $revenueWeek3 = $revenueW3Query !== false ? $revenueW3Query[0] : 0;
      $revenueWeek4 = $revenueW4Query !== false ? $revenueW4Query[0] : 0;
    }

    $countPatientsW1Query = $this->getPatientsByMonthQuery($year, $month, '01', '05', $hosp->getId());
    $countPatientsW2Query = $this->getPatientsByMonthQuery($year, $month, '06', '15', $hosp->getId());
    $countPatientsW3Query = $this->getPatientsByMonthQuery($year, $month, '16', '21', $hosp->getId());
    $countPatientsW4Query = $this->getPatientsByMonthQuery($year, $month, '22', '31', $hosp->getId());

    $patientsWeek1 = $countPatientsW1Query !== false ? $countPatientsW1Query[0] : 0;
    $patientsWeek2 = $countPatientsW2Query !== false ? $countPatientsW2Query[0] : 0;
    $patientsWeek3 = $countPatientsW3Query !== false ? $countPatientsW3Query[0] : 0;
    $patientsWeek4 = $countPatientsW4Query !== false ? $countPatientsW4Query[0] : 0;

    return [
      [
        'name' => '01 au 05',
        'fiches' => $filesWeek1,
        'revenu' => null === $revenueWeek1 ? 0 : $revenueWeek1,
        'patients' => $patientsWeek1,
      ],
      [
        'name' => '06 au 15',
        'fiches' => $filesWeek2,
        'revenu' => null === $revenueWeek2 ? 0 : $revenueWeek2,
        'patients' => $patientsWeek2,
      ],
      [
        'name' => '16 au 21',
        'fiches' => $filesWeek3,
        'revenu' => null === $revenueWeek3 ? 0 : $revenueWeek3,
        'patients' => $patientsWeek3,
      ],
      [
        'name' => '22 au 31',
        'fiches' => $filesWeek4,
        'revenu' => null === $revenueWeek4 ? 0 : $revenueWeek4,
        'patients' => $patientsWeek4,
      ],
    ];
  }

  /**
   * @param $year
   * @return array[]
   * @throws Exception
   */
  public function getStats2ByYearData($year): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();

    $files1 = $this->getFilesByYearQuery($year, '01', $hosp->getId());
    $files2 = $this->getFilesByYearQuery($year, '02', $hosp->getId());
    $files3 = $this->getFilesByYearQuery($year, '03', $hosp->getId());
    $files4 = $this->getFilesByYearQuery($year, '04', $hosp->getId());
    $files5 = $this->getFilesByYearQuery($year, '05', $hosp->getId());
    $files6 = $this->getFilesByYearQuery($year, '06', $hosp->getId());
    $files7 = $this->getFilesByYearQuery($year, '07', $hosp->getId());
    $files8 = $this->getFilesByYearQuery($year, '08', $hosp->getId());
    $files9 = $this->getFilesByYearQuery($year, '09', $hosp->getId());
    $files10 = $this->getFilesByYearQuery($year, '10', $hosp->getId());
    $files11 = $this->getFilesByYearQuery($year, '11', $hosp->getId());
    $files12 = $this->getFilesByYearQuery($year, '12', $hosp->getId());

    $box = $this->boxRepository->findBox($hosp->getId());
    $revenue1 = 0;
    $revenue2 = 0;
    $revenue3 = 0;
    $revenue4 = 0;
    $revenue5 = 0;
    $revenue6 = 0;
    $revenue7 = 0;
    $revenue8 = 0;
    $revenue9 = 0;
    $revenue10 = 0;
    $revenue11 = 0;
    $revenue12 = 0;
    if (null !== $box) {
      $revenue1 = $this->getRevenueByYearQuery($year, '01', $box->getId());
      $revenue2 = $this->getRevenueByYearQuery($year, '02', $box->getId());
      $revenue3 = $this->getRevenueByYearQuery($year, '03', $box->getId());
      $revenue4 = $this->getRevenueByYearQuery($year, '04', $box->getId());
      $revenue5 = $this->getRevenueByYearQuery($year, '05', $box->getId());
      $revenue6 = $this->getRevenueByYearQuery($year, '06', $box->getId());
      $revenue7 = $this->getRevenueByYearQuery($year, '07', $box->getId());
      $revenue8 = $this->getRevenueByYearQuery($year, '08', $box->getId());
      $revenue9 = $this->getRevenueByYearQuery($year, '09', $box->getId());
      $revenue10 = $this->getRevenueByYearQuery($year, '10', $box->getId());
      $revenue11 = $this->getRevenueByYearQuery($year, '11', $box->getId());
      $revenue12 = $this->getRevenueByYearQuery($year, '12', $box->getId());
    }

    $patients1 = $this->getPatientsByYearQuery($year, '01', $hosp->getId());
    $patients2 = $this->getPatientsByYearQuery($year, '02', $hosp->getId());
    $patients3 = $this->getPatientsByYearQuery($year, '03', $hosp->getId());
    $patients4 = $this->getPatientsByYearQuery($year, '04', $hosp->getId());
    $patients5 = $this->getPatientsByYearQuery($year, '05', $hosp->getId());
    $patients6 = $this->getPatientsByYearQuery($year, '06', $hosp->getId());
    $patients7 = $this->getPatientsByYearQuery($year, '07', $hosp->getId());
    $patients8 = $this->getPatientsByYearQuery($year, '08', $hosp->getId());
    $patients9 = $this->getPatientsByYearQuery($year, '09', $hosp->getId());
    $patients10 = $this->getPatientsByYearQuery($year, '10', $hosp->getId());
    $patients11 = $this->getPatientsByYearQuery($year, '11', $hosp->getId());
    $patients12 = $this->getPatientsByYearQuery($year, '12', $hosp->getId());

    return [
      [
        'name' => 'Jan',
        'fiches' => $files1 !== false ? $files1[0] : 0,
        'revenu' => $revenue1[0] ?? 0,
        'patients' => $patients1 !== false ? $patients1[0] : 0,
      ],
      [
        'name' => 'Fév',
        'fiches' => $files2 !== false ? $files2[0] : 0,
        'revenu' => $revenue2[0] ?? 0,
        'patients' => $patients2 !== false ? $patients2[0] : 0,
      ],
      [
        'name' => 'Mar',
        'fiches' => $files3 !== false ? $files3[0] : 0,
        'revenu' => $revenue3[0] ?? 0,
        'patients' => $patients3 !== false ? $patients3[0] : 0,
      ],
      [
        'name' => 'Avr',
        'fiches' => $files4 !== false ? $files4[0] : 0,
        'revenu' => $revenue4[0] ?? 0,
        'patients' => $patients4 !== false ? $patients4[0] : 0,
      ],
      [
        'name' => 'Mai',
        'fiches' => $files5 !== false ? $files5[0] : 0,
        'revenu' => $revenue5[0] ?? 0,
        'patients' => $patients5 !== false ? $patients5[0] : 0,
      ],
      [
        'name' => 'Jui',
        'fiches' => $files6 !== false ? $files6[0] : 0,
        'revenu' => $revenue6[0] ?? 0,
        'patients' => $patients6 !== false ? $patients6[0] : 0,
      ],
      [
        'name' => 'Jui',
        'fiches' => $files7 !== false ? $files7[0] : 0,
        'revenu' => $revenue7[0] ?? 0,
        'patients' => $patients7 !== false ? $patients7[0] : 0,
      ],
      [
        'name' => 'Aoû',
        'fiches' => $files8 !== false ? $files8[0] : 0,
        'revenu' => $revenue8[0] ?? 0,
        'patients' => $patients8 !== false ? $patients8[0] : 0,
      ],
      [
        'name' => 'Sep',
        'fiches' => $files9 !== false ? $files9[0] : 0,
        'revenu' => $revenue9[0] ?? 0,
        'patients' => $patients9 !== false ? $patients9[0] : 0,
      ],
      [
        'name' => 'Oct',
        'fiches' => $files10 !== false ? $files10[0] : 0,
        'revenu' => $revenue10[0] ?? 0,
        'patients' => $patients10 !== false ? $patients10[0] : 0,
      ],
      [
        'name' => 'Nov',
        'fiches' => $files11 !== false ? $files11[0] : 0,
        'revenu' => $revenue11[0] ?? 0,
        'patients' => $patients11 !== false ? $patients11[0] : 0,
      ],
      [
        'name' => 'Déc',
        'fiches' => $files12 !== false ? $files12[0] : 0,
        'revenu' => $revenue12[0] ?? 0,
        'patients' => $patients12 !== false ? $patients12[0] : 0,
      ],
    ];
  }

  /**
   * @param $year
   * @param $month
   * @return bool|array
   * @throws Exception
   */
  public function getMostSalesDrugsData($year, $month): bool|array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    return $this->getMostSalesDrugsQuery($year, $month, $hosp->getId());
  }

  /**
   * @param $year
   * @param $month
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['inputs' => "int|mixed", 'outputs' => "int|mixed", 'difference' => "int|mixed"])]
  public function getBoxByMonth($year, $month): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $box = $this->boxRepository->findBox($hosp->getId());

    $getMonth = $month < 1 ? 12 : $month;
    $getYear = $month < 1 ? ($year - 1) : $year;

    $inputs = 0;
    $outputs = 0;
    if (null !== $box) {
      $inputs = $this->getBoxByMonthQuery($getYear, $getMonth, 'input', $box->getId());
      $outputs = $this->getBoxByMonthQuery($getYear, $getMonth, 'output', $box->getId());
    }

    $iBox = $inputs !== false ? $inputs[0] : 0;
    $oBox = $outputs !== false ? $outputs[0] : 0;

    return [
      'outputs' => $oBox,
      'inputs' => $iBox,
      'difference' => $this->amountService->roundAmount(($iBox - $oBox), 2)
    ];
  }

  /**
   * @param $year
   * @return array
   * @throws Exception
   */
  #[ArrayShape(['inputs' => "int|mixed", 'outputs' => "int|mixed", 'difference' => "int|mixed"])]
  public function getBoxByYear($year): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $box = $this->boxRepository->findBox($hosp->getId());

    $inputs = 0;
    $outputs = 0;
    if (null !== $box) {
      $inputs = $this->getBoxByYearQuery($year, 'input', $box->getId());
      $outputs = $this->getBoxByYearQuery($year, 'output', $box->getId());
    }

    $iBox = $inputs !== false ? $inputs[0] : 0;
    $oBox = $outputs !== false ? $outputs[0] : 0;

    return [
      'outputs' => $oBox,
      'inputs' => $iBox,
      'difference' => $this->amountService->roundAmount(($iBox - $oBox), 2)
    ];
  }

  // ************************************************** Queries ************************************************** //

  /**
   * @param $year
   * @param $month
   * @param $tag
   * @param $boxId
   * @return array|bool
   * @throws Exception
   */
  private function getBoxByMonthQuery($year, $month, $tag, $boxId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) 'sum'
      FROM box_historic bh
       JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND date_format(bh.created_at, '%Y-%m') = :format
       AND bh.tag = :tag
      GROUP BY b.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year.'-'.$month,
      'tag' => $tag
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $tag
   * @param $boxId
   * @return array|bool
   * @throws Exception
   */
  private function getBoxByYearQuery($year, $tag, $boxId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) 'sum'
      FROM box_historic bh
       JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND date_format(bh.created_at, '%Y') = :format
       AND bh.tag = :tag
      GROUP BY b.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year,
      'tag' => $tag
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @return array
   * @throws Exception
   */
  private function getMostSalesDrugsQuery($year, $month, $hospId): array
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       m.id 'id',
       m.wording 'wording',
       COUNT(m.id) 'sales',
       SUM(ms.gain) 'gain'
      FROM medicines_sold ms
       JOIN medicine m ON m.id = ms.medicine_id
       JOIN medicine_invoice mi ON mi.id = ms.invoice_id
       JOIN hospital h ON h.id = m.hospital_id
      WHERE h.id = :hospId
       AND date_format(mi.released, '%Y-%m') = :format
      GROUP BY m.id
      ORDER BY SUM(ms.gain) DESC
      LIMIT 0,10;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month
    ]);

    return $resSet->fetchAllAssociative();
  }

  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @return array|bool
   * @throws Exception
   */
  private function getPatientsByYearQuery($year, $month, $hospId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       COUNT(p.id) 'nombre'
      FROM patient p
       LEFT JOIN hospital h ON h.id = p.hospital_id
      WHERE h.id = :hospId
       AND date_format(p.created_at, '%Y-%m') = :format;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $boxId
   * @return array|bool
   * @throws Exception
   */
  public function getRevenueByYearQuery($year, $month, $boxId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) 'somme'
      FROM box_historic bh
       LEFT JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND date_format(bh.created_at, '%Y-%m') = :format
         AND bh.tag = 'input';
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @return array|bool
   * @throws Exception
   */
  private function getFilesByYearQuery($year, $month, $hospId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
        COUNT(c.id) 'fiches'
        FROM consultation c
         LEFT JOIN hospital h ON h.id = c.hospital_id
        WHERE h.id = :hospId
         AND date_format(c.created_at, '%Y-%m') = :format;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'. $month,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $day1
   * @param $day2
   * @param $hospId
   * @return array|bool
   * @throws Exception
   */
  private function getPatientsByMonthQuery($year, $month, $day1, $day2, $hospId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       COUNT(p.id) 'nombre'
      FROM patient p
       LEFT JOIN hospital h ON h.id = p.hospital_id
      WHERE h.id = :hospId
       AND date_format(p.created_at, '%Y-%m-%d') BETWEEN :format AND :format2
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month.'-'.$day1,
      'format2' => $year.'-'.$month.'-'.$day2,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $day1
   * @param $day2
   * @param $boxId
   * @return array|bool
   * @throws Exception
   */
  private function getRevenueByMonthQuery($year, $month, $day1, $day2, $boxId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
       SUM(bh.amount) 'somme'
      FROM box_historic bh
       LEFT JOIN box b ON b.id = bh.box_id
      WHERE b.id = :boxId
       AND date_format(bh.created_at, '%Y-%m-%d') BETWEEN :format AND :format2
         AND bh.tag = 'input';
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'boxId' => $boxId,
      'format' => $year.'-'.$month.'-'.$day1,
      'format2' => $year.'-'.$month.'-'.$day2,
    ]);

    return $resSet->fetchNumeric();
  }

  /**
   * @param $year
   * @param $month
   * @param $day1
   * @param $day2
   * @param $hospId
   * @return array|bool
   * @throws Exception
   */
  private function getFilesByMonthQuery($year, $month, $day1, $day2, $hospId): array|bool
  {
    $conn = $this->qbService->getConnection();
    $sql = "
      SELECT
        COUNT(c.id) 'fiches'
        FROM consultation c
         LEFT JOIN hospital h ON h.id = c.hospital_id
        WHERE h.id = :hospId
         AND date_format(c.created_at, '%Y-%m-%d') BETWEEN :format AND :format2;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'format' => $year.'-'.$month.'-'.$day1,
      'format2' => $year.'-'.$month.'-'.$day2,
    ]);

    return $resSet->fetchNumeric();
  }

  // ************************************************ End Queries ************************************************ //
}
