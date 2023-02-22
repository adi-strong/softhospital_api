<?php

namespace App\Repository;

use App\Entity\TreatmentInvoiceBasket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TreatmentInvoiceBasket>
 *
 * @method TreatmentInvoiceBasket|null find($id, $lockMode = null, $lockVersion = null)
 * @method TreatmentInvoiceBasket|null findOneBy(array $criteria, array $orderBy = null)
 * @method TreatmentInvoiceBasket[]    findAll()
 * @method TreatmentInvoiceBasket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TreatmentInvoiceBasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TreatmentInvoiceBasket::class);
    }

    public function save(TreatmentInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TreatmentInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return TreatmentInvoiceBasket[] Returns an array of TreatmentInvoiceBasket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TreatmentInvoiceBasket
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
