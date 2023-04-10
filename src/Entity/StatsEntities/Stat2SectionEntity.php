<?php

namespace App\Entity\StatsEntities;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\StatControllers\Stats2SectionController;

#[ApiResource(
  types: 'https://schema.org/Stat2SectionEntity',
  operations: [
    new Get(
      uriTemplate: '/api/get_stats2_by_month/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: Stats2SectionController::class,
      name: 'get_stats2_by_month'
    ),

    new Get(
      uriTemplate: '/api/get_stats2_by_year/{year}',
      requirements: [ 'year' => '\d+' ],
      controller: Stats2SectionController::class,
      name: 'get_stats2_by_year'
    ),

    new Get(
      uriTemplate: '/api/get_most_sales_drugs/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: Stats2SectionController::class,
      name: 'get_most_sales_drugs'
    ),

    new Get(
      uriTemplate: '/api/get_box_by_month/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: Stats2SectionController::class,
      name: 'get_box_by_month'
    ),

    new Get(
      uriTemplate: '/api/get_box_by_year/{year}',
      requirements: [ 'year' => '\d+' ],
      controller: Stats2SectionController::class,
      name: 'get_box_by_year'
    ),
  ]
)]
class Stat2SectionEntity
{
}
