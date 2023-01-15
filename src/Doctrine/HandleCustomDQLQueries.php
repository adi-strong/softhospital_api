<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Covenant;
use App\Entity\ImageObject;
use App\Entity\Patient;
use App\Entity\PersonalImageObject;
use App\Entity\User;
use App\Services\HandleCurrentUserService;
use Doctrine\ORM\QueryBuilder;

class HandleCustomDQLQueries implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
  public function __construct(private readonly ?HandleCurrentUserService $currentUser)
  {
  }

  private function rootAlias($qb)
  {
    return $qb->getRootAliases()[0];
  }

  private function addWhere(QueryBuilder $qb, string $resourceClass)
  {
    $user = $this->currentUser->getUser();
    // Patient
    if (($resourceClass === Patient::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->andWhere("$alias.uId = :uId");
        $qb->setParameter('uId', $user->getUId());
      }
      else {
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
      }
    }
    // End Patient

    /** ----------------------------------------------------------------------------------- **/

    // Covenant
    elseif (($resourceClass === Covenant::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->andWhere("$alias.uId = :uId");
        $qb->setParameter('uId', $user->getUId());
      }
    }
    // End Covenant

    /** ----------------------------------------------------------------------------------- **/

    // ImageObject
    elseif (($resourceClass === ImageObject::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->andWhere("$alias.uId = :uId");
        $qb->setParameter('uId', $user->getUId());
      }
    }
    // End ImageObject

    /** ----------------------------------------------------------------------------------- **/

    // PersonalImageObject
    elseif (($resourceClass === PersonalImageObject::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->andWhere("$alias.uId = :uId");
        $qb->setParameter('uId', $user->getUId());
      }
    }
    // End PersonalImageObject

    /** ----------------------------------------------------------------------------------- **/

    // User
    if (($resourceClass === User::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->andWhere("$alias.uId = :uId");
        $qb->setParameter('uId', $user->getUId());
      }
      else {
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
      }
    }
    // End User

    /** ----------------------------------------------------------------------------------- **/
  }

  public function applyToCollection(
    QueryBuilder $queryBuilder,
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass,
    Operation $operation = null,
    array $context = []): void
  {
    $this->addWhere($queryBuilder, $resourceClass);
  }

  public function applyToItem(
    QueryBuilder $queryBuilder,
    QueryNameGeneratorInterface $queryNameGenerator,
    string $resourceClass,
    array $identifiers,
    Operation $operation = null,
    array $context = []): void
  {
    $this->addWhere($queryBuilder, $resourceClass);
  }
}
