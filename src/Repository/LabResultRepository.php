<?php

namespace App\Repository;

use App\Entity\Exam;
use App\Entity\Lab;
use App\Entity\LabResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LabResult>
 *
 * @method LabResult|null find($id, $lockMode = null, $lockVersion = null)
 * @method LabResult|null findOneBy(array $criteria, array $orderBy = null)
 * @method LabResult[]    findAll()
 * @method LabResult[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LabResult::class);
    }

    public function save(LabResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(LabResult $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return LabResult[] Returns an array of LabResult objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LabResult
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findLabExam(Exam $exam, Lab $lab)
  {
    $qb = $this->createQueryBuilder('lr')
      ->join('lr.lab', 'l')
      ->join('lr.exam', 'e', 'WITH', 'e.id = :exam')
      ->where('lr.lab = :lab')
      ->setParameter('lab', $lab)
      ->setParameter('exam', $exam);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
