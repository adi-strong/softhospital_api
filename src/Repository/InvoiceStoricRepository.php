<?php

namespace App\Repository;

use App\Entity\InvoiceStoric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceStoric>
 *
 * @method InvoiceStoric|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceStoric|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceStoric[]    findAll()
 * @method InvoiceStoric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceStoricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceStoric::class);
    }

    public function save(InvoiceStoric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InvoiceStoric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return InvoiceStoric[] Returns an array of InvoiceStoric objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InvoiceStoric
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
