<?php

namespace App\Repository;

use App\Entity\Act;
use App\Entity\ActsInvoiceBasket;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActsInvoiceBasket>
 *
 * @method ActsInvoiceBasket|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActsInvoiceBasket|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActsInvoiceBasket[]    findAll()
 * @method ActsInvoiceBasket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActsInvoiceBasketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActsInvoiceBasket::class);
    }

    public function save(ActsInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ActsInvoiceBasket $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return ActsInvoiceBasket[] Returns an array of ActsInvoiceBasket objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ActsInvoiceBasket
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

  public function findInvoiceAct(Act $act, Invoice $invoice): ?ActsInvoiceBasket
  {
    $qb = $this->createQueryBuilder('aib')
      ->join('aib.invoice', 'i', 'WITH', 'i.id = :invoice')
      ->where('aib.act = :act')
      ->setParameter('act', $act)
      ->setParameter('invoice', $invoice);

    $result = null;
    try {
      $result = $qb->getQuery()->getOneOrNullResult();
    } catch (NonUniqueResultException $e) { }

    return $result;
  }
}
