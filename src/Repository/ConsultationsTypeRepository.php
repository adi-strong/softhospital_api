<?php

namespace App\Repository;

use App\Entity\ConsultationsType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConsultationsType>
 *
 * @method ConsultationsType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsultationsType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsultationsType[]    findAll()
 * @method ConsultationsType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsultationsTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsultationsType::class);
    }

    public function save(ConsultationsType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConsultationsType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ConsultationsType[] Returns an array of ConsultationsType objects
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

//    public function findOneBySomeField($value): ?ConsultationsType
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  /**
   * @throws Exception
   */
  public function countFiles($month, $year, $hospitalId): array
  {
    /*$queryBuilder = $this->createQueryBuilder('f')->select('COUNT(f.id) AS nbFiles');
    $queryBuilder
      ->where($queryBuilder->expr()->eq("DATE_FORMAT(f.createdAt, '%Y-%m')", "':format'"))
      ->setParameter('format', $year.'-'.$month);

    $result = null;
    try {
      $result = $queryBuilder->getQuery()->getSingleScalarResult();
    } catch (NoResultException|NonUniqueResultException $e) { }

    return $result;*/
    $conn = $this->getEntityManager()->getConnection();

    $sql = "
      select count(c.id) nbFiles
        FROM consultations_type c 
          LEFT JOIN hospital h ON h.id = c.hospital_id
        WHERE c.hospital_id IS NOT NULL 
          AND h.id = :hospitalId
          AND date_format(c.created_at, '%Y-%m') = :format
          GROUP BY h.id
    ";

    $stm = $conn->prepare($sql);
    $resSet = $stm->executeQuery([
      'hospitalId' => $hospitalId,
      'format' => $year.'-'.$month
    ]);

    return $resSet->fetchAllAssociative();
  }
}
