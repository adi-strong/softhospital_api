<?php

namespace App\Repository;

use App\Entity\DestockingOfMedicines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DestockingOfMedicines>
 *
 * @method DestockingOfMedicines|null find($id, $lockMode = null, $lockVersion = null)
 * @method DestockingOfMedicines|null findOneBy(array $criteria, array $orderBy = null)
 * @method DestockingOfMedicines[]    findAll()
 * @method DestockingOfMedicines[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DestockingOfMedicinesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DestockingOfMedicines::class);
    }

    public function save(DestockingOfMedicines $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DestockingOfMedicines $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DestockingOfMedicines[] Returns an array of DestockingOfMedicines objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DestockingOfMedicines
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
