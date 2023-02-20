<?php

namespace App\Repository;

use App\Entity\MedicineInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicineInvoice>
 *
 * @method MedicineInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicineInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicineInvoice[]    findAll()
 * @method MedicineInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicineInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicineInvoice::class);
    }

    public function save(MedicineInvoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicineInvoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MedicineInvoice[] Returns an array of MedicineInvoice objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MedicineInvoice
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
