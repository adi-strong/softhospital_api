<?php

namespace App\Repository;

use App\Entity\DestockMedicineForHospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DestockMedicineForHospital>
 *
 * @method DestockMedicineForHospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method DestockMedicineForHospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method DestockMedicineForHospital[]    findAll()
 * @method DestockMedicineForHospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DestockMedicineForHospitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DestockMedicineForHospital::class);
    }

    public function save(DestockMedicineForHospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DestockMedicineForHospital $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DestockMedicineForHospital[] Returns an array of DestockMedicineForHospital objects
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

//    public function findOneBySomeField($value): ?DestockMedicineForHospital
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
