<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\Exam;
use App\Entity\Lab;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lab>
 *
 * @method Lab|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lab|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lab[]    findAll()
 * @method Lab[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LabRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lab::class);
    }

    public function save(Lab $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lab $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Lab[] Returns an array of Lab objects
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

//    public function findOneBySomeField($value): ?Lab
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findLabExam(Exam $exam, Lab $lab): ?Lab
  {
    $qb = $this->createQueryBuilder('l')
      ->join('l.labResults', 'r')
      ->where('l.id = :lab')
      ->andWhere('r.exam = :exam');

    $result = null;
    $query = $qb
      ->setParameter('lab', $lab)
      ->setParameter('exam', $exam);

    try {
      $result = $query->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
