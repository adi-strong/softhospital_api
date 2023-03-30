<?php

namespace App\Entity\StatsEntities;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Controller\StatControllers\Stats1SectionController;

#[ApiResource(
  types: 'https://schema.org/Stat1SectionEntity',
  operations: [
    new Get(
      uriTemplate: '/api/get_file_stats/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller: Stats1SectionController::class,
      name: 'get_file_stats'
    ),

    new Get(
      uriTemplate: '/api/get_file_stats_by_year/{year}',
      requirements: ['year' => '\d+'],
      controller:  Stats1SectionController::class,
      name: 'get_file_stats_by_year'
    ),

    // **************************************************************************************
    // **************************************************************************************

    new Get(
      uriTemplate: '/api/get_revenue_stats/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller:  Stats1SectionController::class,
      name: 'get_revenue_stats'
    ),

    new Get(
      uriTemplate: '/api/get_revenue_by_year_stats/{year}',
      requirements: ['year' => '\d+'],
      controller:  Stats1SectionController::class,
      name: 'get_revenue_by_year_stats'
    ),

    // **************************************************************************************
    // **************************************************************************************

    new Get(
      uriTemplate: '/api/get_patients_stats/{year}/{month}',
      requirements: [ 'year' => '\d+', 'month' => '\d+' ],
      controller:  Stats1SectionController::class,
      name: 'get_patients_stats'
    ),

    new Get(
      uriTemplate: '/api/get_patients_by_year_stats/{year}',
      requirements: ['year' => '\d+'],
      controller:  Stats1SectionController::class,
      name: 'get_patients_by_year_stats'
    ),
  ]
)]
class Stat1SectionEntity
{
}
