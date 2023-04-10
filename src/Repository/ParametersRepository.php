<?php

namespace App\Repository;

use ApiPlatform\OpenApi\Model\Parameter;
use App\Entity\Hospital;
use App\Entity\Parameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Parameters>
 *
 * @method Parameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameters[]    findAll()
 * @method Parameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameters::class);
    }

    public function save(Parameters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Parameters $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

  /**
   * @throws NonUniqueResultException
   */
  public function findLastParameters($hospitalId)
  {
    $qb = $this->createQueryBuilder('p')
      ->addSelect('p.currency')
      ->join('p.hospital', 'h')
      ->andWhere('p.hospital = :hospital')
      ->setParameter('hospital', $hospitalId)
      ->orderBy('p.id', 'DESC')
      ->setMaxResults(1);
    return $qb->getQuery()->getOneOrNullResult();
  }

//    /**
//     * @return Parameters[] Returns an array of Parameters objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Parameters
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findLastParameter(Hospital $hospital): array|float|int|string
  {
    $qb = $this->createQueryBuilder('p')
      ->select('p.currency')
      ->join('p.hospital', 'h', 'WITH', 'h.id = :hospital')
      ->orderBy('p.id', 'DESC')
      ->setParameter(':hospital', $hospital)
      ->setMaxResults(1);

    return $qb->getQuery()->getSingleColumnResult();
  }

  /**
   * @throws NonUniqueResultException
   */
  public function getLastParam(Hospital $hospital)
  {
    $qb = $this->createQueryBuilder('p')
      ->join('p.hospital', 'h')
      ->where('p.hospital = :hospId')
      ->setParameter('hospId', $hospital)
      ->setMaxResults(1)
      ->orderBy('p.id', 'DESC');

    return $qb->getQuery()->getOneOrNullResult();
  }

  /**
   * @throws NonUniqueResultException
   */
  public function findParameters(Hospital $hospital)
  {
    $qb = $this->createQueryBuilder('p')
      ->join('p.hospital', 'h')
      ->where('p.hospital = :hosp')
      ->setParameter('hosp', $hospital)
      ->orderBy('p.id', 'DESC')
      ->setMaxResults(1);

    return $qb->getQuery()->getOneOrNullResult();
  }
}
