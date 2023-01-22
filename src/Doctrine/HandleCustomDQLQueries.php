<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Box;
use App\Entity\BoxExpense;
use App\Entity\BoxInput;
use App\Entity\BoxOutput;
use App\Entity\Covenant;
use App\Entity\ExpenseCategory;
use App\Entity\ImageObject;
use App\Entity\Parameters;
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
    if (
      ($resourceClass === Patient::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h');
        $qb->andWhere("$alias.hospital", ':hospital');
        $qb->setParameter('uId', $user->getHospital() ?? $user->getHospitalCenter());
      }
      else {
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
      }
    }
    // End Patient

    /** ----------------------------------------------------------------------------------- **/

    // Covenant
    if (
      ($resourceClass === Covenant::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospitalCenter() ?? $user->getHospital());
      }
    }
    // End Covenant

    /** ----------------------------------------------------------------------------------- **/

    // ImageObject
    if (
      ($resourceClass === ImageObject::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospitalCenter() ?? $user->getHospital());
      }
    }
    // End ImageObject

    /** ----------------------------------------------------------------------------------- **/

    // PersonalImageObject
    if (
      ($resourceClass === PersonalImageObject::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.user", 'u');
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
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

    // Parameters
    if (($resourceClass === Parameters::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Parameters

    /** ----------------------------------------------------------------------------------- **/

    // Box
    if (($resourceClass === Box::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Box

    /** ----------------------------------------------------------------------------------- **/

    // BoxInput
    if (($resourceClass === BoxInput::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End BoxInput

    /** ----------------------------------------------------------------------------------- **/

    // BoxOutput
    if (($resourceClass === BoxOutput::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End BoxOutput

    /** ----------------------------------------------------------------------------------- **/

    // BoxExpense
    if (($resourceClass === BoxExpense::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End BoxExpense

    /** ----------------------------------------------------------------------------------- **/

    // ExpenseCategory
    if (($resourceClass === ExpenseCategory::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End ExpenseCategory

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
