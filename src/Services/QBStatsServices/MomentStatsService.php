<?php

namespace App\Services\QBStatsServices;

class MomentStatsService
{
  public function getLastMonth($month): int|string
  {
    $lastMonth = (int) $month - 1;
    return $lastMonth <= 9 ? '0'.$lastMonth : $lastMonth;
  }

  public function getLastYear($year): int
  {
    return (int) $year - 1;
  }
}
