<?php

namespace App\Repository;

use App\Entity\Nursing;
use App\Entity\Treatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nursing>
 *
 * @method Nursing|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nursing|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nursing[]    findAll()
 * @method Nursing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NursingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nursing::class);
    }

    public function save(Nursing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Nursing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Nursing[] Returns an array of Nursing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nursing
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findTreatmentNurse(Treatment $treatment, Nursing $nursing): ?Nursing
  {
    $qb = $this->createQueryBuilder('n')
      ->join('n.nursingTreatments', 'nrs')
      ->where('n.id = :nursing')
      ->andWhere('nrs.treatment = :treatment')
      ->setParameter('nursing', $nursing)
      ->setParameter('treatment', $treatment);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
