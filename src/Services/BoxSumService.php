<?php

namespace App\Services;

use App\Entity\Box;
use App\Repository\BoxHistoricRepository;

class BoxSumService
{
  public function __construct(private readonly BoxHistoricRepository $repository)
  {
  }

  public function getBoxSum(Box $box): float|string|null
  {
    $inputsSum = 0;
    $historics = $this->repository->findBoxSum($box, 'input');
    foreach ($historics as $historic)
      $inputsSum += $historic->getAmount();

    return $inputsSum;
  }

  public function getBoxOutputSum(Box $box): float|string|null
  {
    $outputsSum = 0;
    $historics = $this->repository->findBoxSum($box, 'output');
    foreach ($historics as $historic)
      $outputsSum += $historic->getAmount();

    return $outputsSum;
  }
}
