<?php

namespace App\Repository;

use App\Entity\MedicinesSold;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicinesSold>
 *
 * @method MedicinesSold|null find($id, $lockMode = null, $lockVersion = null)
 * @method MedicinesSold|null findOneBy(array $criteria, array $orderBy = null)
 * @method MedicinesSold[]    findAll()
 * @method MedicinesSold[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedicinesSoldRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicinesSold::class);
    }

    public function save(MedicinesSold $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(MedicinesSold $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return MedicinesSold[] Returns an array of MedicinesSold objects
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

//    public function findOneBySomeField($value): ?MedicinesSold
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
