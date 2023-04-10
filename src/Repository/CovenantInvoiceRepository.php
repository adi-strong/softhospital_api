<?php

namespace App\Repository;

use App\Entity\CovenantInvoice;
use App\Entity\Hospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CovenantInvoice>
 *
 * @method CovenantInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method CovenantInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method CovenantInvoice[]    findAll()
 * @method CovenantInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CovenantInvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CovenantInvoice::class);
    }

    public function save(CovenantInvoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CovenantInvoice $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CovenantInvoice[] Returns an array of CovenantInvoice objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CovenantInvoice
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function getCovenantInvoice($year, $month, Hospital $hospital): ?CovenantInvoice
  {
    $qb = $this->createQueryBuilder('ci')
      ->join('ci.hospital', 'h')
      ->where('ci.month = :month')
      ->andWhere('ci.hospital = :hosp')
      ->andWhere('ci.year = :year')
      ->setParameter('hosp', $hospital)
      ->setParameter('month', $month)
      ->setParameter('year', $year);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
