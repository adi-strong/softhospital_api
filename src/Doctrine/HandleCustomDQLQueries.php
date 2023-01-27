<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Agent;
use App\Entity\Box;
use App\Entity\BoxExpense;
use App\Entity\BoxInput;
use App\Entity\BoxOutput;
use App\Entity\Covenant;
use App\Entity\Department;
use App\Entity\ExpenseCategory;
use App\Entity\ImageObject;
use App\Entity\Office;
use App\Entity\Parameters;
use App\Entity\Patient;
use App\Entity\PersonalImageObject;
use App\Entity\Service;
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
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter())
          ->setParameter('isDeleted', false);
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
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter())
          ->setParameter('isDeleted', false);
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
        $qb
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->andWhere("$alias.uId = :uId")
          ->setParameter('uId', $user->getUId())
          ->setParameter('isDeleted', false);
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

    // Department
    if (($resourceClass === Department::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter())
          ->setParameter('isDeleted', false);
      }
    }
    // End Department

    /** ----------------------------------------------------------------------------------- **/

    // Service
    if (($resourceClass === Service::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter())
          ->setParameter('isDeleted', false);
      }
    }
    // End Service

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

    // Office
    if (($resourceClass === Office::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->setParameter('isDeleted', false)
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Office

    /** ----------------------------------------------------------------------------------- **/

    // Agent
    if (($resourceClass === Agent::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->setParameter('isDeleted', false)
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Agent

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
