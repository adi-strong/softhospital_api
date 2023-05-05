<?php

namespace App\Repository;

use App\Entity\ResetPassNotifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResetPassNotifier>
 *
 * @method ResetPassNotifier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResetPassNotifier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResetPassNotifier[]    findAll()
 * @method ResetPassNotifier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResetPassNotifierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPassNotifier::class);
    }

    public function save(ResetPassNotifier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ResetPassNotifier $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ResetPassNotifier[] Returns an array of ResetPassNotifier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResetPassNotifier
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
