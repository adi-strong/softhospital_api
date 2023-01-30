<?php

namespace App\Repository;

use App\Entity\Box;
use App\Entity\BoxHistoric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BoxHistoric>
 *
 * @method BoxHistoric|null find($id, $lockMode = null, $lockVersion = null)
 * @method BoxHistoric|null findOneBy(array $criteria, array $orderBy = null)
 * @method BoxHistoric[]    findAll()
 * @method BoxHistoric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoxHistoricRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BoxHistoric::class);
    }

    public function save(BoxHistoric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BoxHistoric $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return BoxHistoric[] Returns an array of BoxHistoric objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BoxHistoric
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;

  /**
   * @param Box $box
   * @param string $tag
   * @return BoxHistoric[]
   */
  public function findBoxSum(Box $box, string $tag): array
  {
    $qb = $this->createQueryBuilder('h')
      ->join('h.box', 'b')
      ->andWhere('h.box = :box')
      ->andWhere('h.tag = :tag');

    $parameters = $qb
      ->setParameter('box', $box)
      ->setParameter('tag', $tag);

    $query = $parameters->getQuery();

    return $query->getResult();
  }
}
