<?php

namespace App\Repository;

use App\Entity\NursingMedicines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NursingMedicines>
 *
 * @method NursingMedicines|null find($id, $lockMode = null, $lockVersion = null)
 * @method NursingMedicines|null findOneBy(array $criteria, array $orderBy = null)
 * @method NursingMedicines[]    findAll()
 * @method NursingMedicines[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NursingMedicinesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NursingMedicines::class);
    }

    public function save(NursingMedicines $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NursingMedicines $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NursingMedicines[] Returns an array of NursingMedicines objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NursingMedicines
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
