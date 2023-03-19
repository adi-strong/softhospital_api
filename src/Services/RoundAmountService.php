<?php

namespace App\Services;

class RoundAmountService
{
  public function roundAmount(float $amount, int $precision): float
  {
    return (float) ( (int) ($amount * pow(10, $precision) + .5) ) / pow(10, $precision);
  }
}
