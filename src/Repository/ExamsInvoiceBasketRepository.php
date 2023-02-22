<?php

namespace App\Repository;

use App\Entity\ExamsInvoiceBasket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExamsInvoiceBasket>
 *
 * @method ExamsInvoiceBasket|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamsInvoiceBasket|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamsInvoiceBasket[]    findAll()
 * @method ExamsInvoiceBasket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamsInvoiceBasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamsInvoiceBasket::class);
    }

    public function save(ExamsInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExamsInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ExamsInvoiceBasket[] Returns an array of ExamsInvoiceBasket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ExamsInvoiceBasket
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
