<?php

namespace App\Services\QBStatsServices;

use App\Services\HandleCurrentUserService;
use App\Services\QueryBuilderConnectionService;
use App\Services\RoundAmountService;
use DateTime;
use Doctrine\DBAL\Exception;
use JetBrains\PhpStorm\NoReturn;

class CovenantInvoicesQueryBuilderService
{
  public function __construct(
    private readonly QueryBuilderConnectionService $connectionService,
    private readonly RoundAmountService $amountService,
    private readonly HandleCurrentUserService $user) { }

  /**
   * @param $year
   * @param $month
   * @param $covenantId
   * @return array
   * @throws Exception
   * @throws \Exception
   */
  #[NoReturn]
  public function getInvoicesData($year, $month, $covenantId): array
  {
    $hosp = $this->user->getHospital() ?? $this->user->getHospitalCenter();
    $invoiceSubTotal = 0;
    $query1DataValues = $this->getInvoicesQuery($year, $month, $hosp->getId(), $covenantId); // invoices query
    $invoice = $this->getInvoiceQuery($year, $month, $covenantId);
    $date = new DateTime();
    $currency = $query1DataValues[0]['currency'] ?? null;

    $hospPrice = 0;
    $filesPrice = 0;
    foreach ($query1DataValues as $q1Value) {
      if ($q1Value['bedPrice'] != null) {
        $hospReleasedAt = new DateTime($q1Value['hospReleasedAt']);
        $hospLeaveAt = $q1Value['hospLeaveAt'] !== null ? new DateTime($q1Value['hospLeaveAt']) : null;
        $bedPrice = $q1Value['bedPrice'];
        $daysCounter = null !== $hospLeaveAt
          ? $hospLeaveAt->diff($hospReleasedAt)->days + 1
          : $date->diff($hospReleasedAt)->days + 1;

        $hospPrice += $this->amountService->roundAmount(($bedPrice * $daysCounter), 2);
      }

      if ($q1Value['filePrice'] !== null) $filesPrice += $q1Value['filePrice'];
    }
    $invoiceSubTotal += $hospPrice;
    $invoiceSubTotal += $filesPrice;

    $query2DataValues = $this->getNursingInvoiceQuery($year, $month, $hosp->getId(), $covenantId);
    $query3DataValues = $this->getInvoiceActsBasketsQuery($year, $month, $covenantId);
    $query4DataValues = $this->getInvoiceExamsBasketsQuery($year, $month, $covenantId);

    $totalNursingPrice = 0;
    foreach ($query2DataValues as $q2Value) {
      if ($q2Value['price'] !== null) {
        $totalNursingPrice += $q2Value['price'];
      }
    }
    $invoiceSubTotal += $totalNursingPrice;

    $totalActsPrice = 0;
    foreach ($query3DataValues as $q3Value) {
      if ($q3Value !== null) $totalActsPrice += $q3Value['price'];
    }
    $invoiceSubTotal += $totalActsPrice;

    $totalExamsPrice = 0;
    foreach ($query4DataValues as $q4Value) {
      if ($q4Value !== null) $totalExamsPrice += $q4Value['price'];
    }
    $invoiceSubTotal += $totalExamsPrice;

    // dd($query1DataValues);

    return [
      'subTotal' => $invoiceSubTotal,
      'filesPrice' => $filesPrice,
      //'invoice' => $query1DataValues,
      'year' => $year,
      'month' => $month,
      'totalActsBaskets' => $totalActsPrice,
      'totalExamsBaskets' => $totalExamsPrice,
      'totalNursingPrice' => $totalNursingPrice,
      'hospPrice' => $hospPrice,
      'currency' => $currency,
      'isInvoiceExists' => $invoice,
    ];
  }

  /**
   * @param $year
   * @param $month
   * @param $covenantId
   * @return false|mixed
   * @throws Exception
   */
  private function getInvoiceQuery($year, $month, $covenantId): mixed
  {
    $conn = $this->connectionService->getConnection();
    $sql = "
      SELECT
       ci.id 'invoiceId',
       ci.sub_total 'subTotal',
       co.id 'id'
      FROM covenant_invoice ci
       JOIN covenant co ON co.id = ci.covenant_id
      WHERE date_format(ci.released_at, '%Y-%m') = :format
       AND co.id = :covenantId;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'covenantId' => $covenantId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchOne();
  }

  /**
   * @param $year
   * @param $month
   * @param $covenantId
   * @return array
   * @throws Exception
   */
  private function getInvoiceExamsBasketsQuery($year, $month, $covenantId): array
  {
    $conn = $this->connectionService->getConnection();
    $sql = "
      SELECT
       eib.id 'id',
       eib.price 'price'
      FROM exams_invoice_basket eib
       JOIN invoice i ON i.id = eib.invoice_id
       JOIN patient p ON p.id = i.patient_id
       JOIN covenant co ON co.id = p.covenant_id
      WHERE co.id = :covenantId
       AND date_format(i.released_at, '%Y-%m') = :format;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'covenantId' => $covenantId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchAllAssociative();
  }

  /**
   * @param $year
   * @param $month
   * @param $covenantId
   * @return array
   * @throws Exception
   */
  private function getInvoiceActsBasketsQuery($year, $month, $covenantId): array
  {
    $conn = $this->connectionService->getConnection();
    $sql = "
      SELECT
       aib.id 'id',
       aib.price 'price'
      FROM acts_invoice_basket aib
       JOIN invoice i ON i.id = aib.invoice_id
       JOIN patient p ON p.id = i.patient_id
       JOIN covenant co ON co.id = p.covenant_id
      WHERE co.id = :covenantId
       AND date_format(i.released_at, '%Y-%m') = :format;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'covenantId' => $covenantId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchAllAssociative();
  }

  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @param $covenantId
   * @return array
   * @throws Exception
   */
  public function getNursingInvoiceQuery($year, $month, $hospId, $covenantId): array
  {
    $conn = $this->connectionService->getConnection();
    $sql = "
      SELECT
       n.id 'id',
       t.wording 'wording',
       nt.medicines 'medicines',
       nt.price 'price'
      FROM nursing n
       JOIN hospital h ON h.id = n.hospital_id
       JOIN nursing_treatment nt ON nt.nursing_id = n.id
       JOIN treatment t ON t.id = nt.treatment_id
       JOIN patient p ON p.id = n.patient_id
       JOIN covenant co ON co.id = p.covenant_id
      WHERE h.id = :hospId
       AND date_format(n.created_at, '%Y-%m') = :format
       AND co.id = :covenantId;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'covenantId' => $covenantId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchAllAssociative();
  }

  /**
   * @param $year
   * @param $month
   * @param $hospId
   * @param $covenantId
   * @return array
   * @throws Exception
   */
  private function getInvoicesQuery($year, $month, $hospId, $covenantId): array
  {
    $conn = $this->connectionService->getConnection();
    $sql = "
      SELECT
       i.id 'id',
       SUM(i.sub_total) 'subTotal',
       f.price 'filePrice',
       ho.price 'hospPrice',
       ho.released_at 'hospReleasedAt',
       ho.leave_at 'hospLeaveAt',
       n.id 'nursingId',
       i.currency 'currency',
       p.name 'patient',
       c.denomination 'covenant',
       b.price  'bedPrice'
      FROM invoice i
       JOIN hospital h ON h.id = i.hospital_id
       JOIN patient p ON p.id = i.patient_id
       JOIN covenant c ON c.id = p.covenant_id
       JOIN consultation co ON co.id = i.consultation_id
       LEFT JOIN consultations_type f ON f.id = co.file_id
       LEFT JOIN hospitalization ho ON ho.consultation_id = i.id
       LEFT JOIN nursing n ON n.consultation_id = i.id
       LEFT JOIN bed b ON b.id = ho.bed_id
      WHERE h.id = :hospId
       AND c.id = :covenantId
       AND date_format(i.released_at, '%Y-%m') = :format
      GROUP BY i.id;
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospId' => $hospId,
      'covenantId' => $covenantId,
      'format' => $year.'-'.$month,
    ]);

    return $resSet->fetchAllAssociative();
  }
}
