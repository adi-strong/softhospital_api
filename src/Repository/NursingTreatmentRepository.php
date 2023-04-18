<?php

namespace App\Repository;

use App\Entity\Nursing;
use App\Entity\NursingTreatment;
use App\Entity\Treatment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<NursingTreatment>
 *
 * @method NursingTreatment|null find($id, $lockMode = null, $lockVersion = null)
 * @method NursingTreatment|null findOneBy(array $criteria, array $orderBy = null)
 * @method NursingTreatment[]    findAll()
 * @method NursingTreatment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NursingTreatmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NursingTreatment::class);
    }

    public function save(NursingTreatment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(NursingTreatment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NursingTreatment[] Returns an array of NursingTreatment objects
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

//    public function findOneBySomeField($value): ?NursingTreatment
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findNursingTreatment(Treatment $treatment, Nursing $nursing): ?NursingTreatment
  {
    $qb = $this->createQueryBuilder('nt')
      ->join('nt.nursing', 'n', 'WITH', 'n.id = :nursing')
      ->where('nt.treatment = :treatment')
      ->setParameter('treatment', $treatment)
      ->setParameter('nursing', $nursing);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }

  public function findNursingTreatment2($treatmentId, Nursing $nursing): ?NursingTreatment
  {
    $qb = $this->createQueryBuilder('nt')
      ->join('nt.nursing', 'n', 'WITH', 'n.id = :nursing')
      ->where('nt.treatment = :treatment')
      ->setParameter('treatment', $treatmentId)
      ->setParameter('nursing', $nursing);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
