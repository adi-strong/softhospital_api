<?php

namespace App\Repository;

use App\Entity\DrugstoreSupplyMedicine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DrugstoreSupplyMedicine>
 *
 * @method DrugstoreSupplyMedicine|null find($id, $lockMode = null, $lockVersion = null)
 * @method DrugstoreSupplyMedicine|null findOneBy(array $criteria, array $orderBy = null)
 * @method DrugstoreSupplyMedicine[]    findAll()
 * @method DrugstoreSupplyMedicine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrugstoreSupplyMedicineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DrugstoreSupplyMedicine::class);
    }

    public function save(DrugstoreSupplyMedicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DrugstoreSupplyMedicine $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return DrugstoreSupplyMedicine[] Returns an array of DrugstoreSupplyMedicine objects
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

//    public function findOneBySomeField($value): ?DrugstoreSupplyMedicine
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
