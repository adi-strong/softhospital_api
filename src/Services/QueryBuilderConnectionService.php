<?php

namespace App\Services;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

class QueryBuilderConnectionService
{
  public function __construct(private readonly EntityManagerInterface $em) { }

  public function getConnection(): Connection
  {
    return $this->em->getConnection();
  }

  public function getEntityManager(): EntityManagerInterface
  {
    return $this->em;
  }
}
