<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Act;
use App\Entity\ActCategory;
use App\Entity\Agent;
use App\Entity\Appointment;
use App\Entity\Bed;
use App\Entity\Bedroom;
use App\Entity\BedroomCategory;
use App\Entity\Box;
use App\Entity\BoxExpense;
use App\Entity\BoxInput;
use App\Entity\BoxOutput;
use App\Entity\Consultation;
use App\Entity\ConsultationsType;
use App\Entity\ConsumptionUnit;
use App\Entity\Covenant;
use App\Entity\Department;
use App\Entity\DrugstoreSupply;
use App\Entity\Exam;
use App\Entity\ExamCategory;
use App\Entity\ExpenseCategory;
use App\Entity\ImageObject;
use App\Entity\Invoice;
use App\Entity\Lab;
use App\Entity\Medicine;
use App\Entity\MedicineCategories;
use App\Entity\MedicineInvoice;
use App\Entity\MedicinesSold;
use App\Entity\MedicineSubCategories;
use App\Entity\Office;
use App\Entity\Parameters;
use App\Entity\Patient;
use App\Entity\PersonalImageObject;
use App\Entity\Prescription;
use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\Treatment;
use App\Entity\TreatmentCategory;
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
    // End Office

    /** ----------------------------------------------------------------------------------- **/

    // ConsultationsType
    if (($resourceClass === ConsultationsType::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End ConsultationsType

    /** ----------------------------------------------------------------------------------- **/

    // Act
    if (($resourceClass === Act::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End Act

    /** ----------------------------------------------------------------------------------- **/

    // Exam
    if (($resourceClass === Exam::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End Exam

    /** ----------------------------------------------------------------------------------- **/

    // ExamCategory
    if (($resourceClass === ExamCategory::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End ExamCategory

    /** ----------------------------------------------------------------------------------- **/

    // ActCategory
    if (($resourceClass === ActCategory::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End ActCategory

    /** ----------------------------------------------------------------------------------- **/

    // TreatmentCategory
    if (($resourceClass === TreatmentCategory::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End TreatmentCategory

    /** ----------------------------------------------------------------------------------- **/

    // Treatment
    if (($resourceClass === Treatment::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End Treatment

    /** ----------------------------------------------------------------------------------- **/

    // BedroomCategory
    if (($resourceClass === BedroomCategory::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End BedroomCategory

    /** ----------------------------------------------------------------------------------- **/

    // Bedroom
    if (($resourceClass === Bedroom::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End Bedroom

    /** ----------------------------------------------------------------------------------- **/

    // Bed
    if (($resourceClass === Bed::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
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
    // End Bed

    /** ----------------------------------------------------------------------------------- **/

    // ConsumptionUnit
    if (($resourceClass === ConsumptionUnit::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End ConsumptionUnit

    /** ----------------------------------------------------------------------------------- **/

    // MedicineCategories
    if (($resourceClass === MedicineCategories::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End MedicineCategories

    /** ----------------------------------------------------------------------------------- **/

    // MedicineSubCategories
    if (($resourceClass === MedicineSubCategories::class) && !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb
          ->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End MedicineSubCategories

    /** ----------------------------------------------------------------------------------- **/

    // Medicine
    if (
      ($resourceClass === Medicine::class) &&
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
    // End Medicine

    /** ----------------------------------------------------------------------------------- **/

    // Provider
    if (
      ($resourceClass === Provider::class) &&
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
    // End Provider

    /** ----------------------------------------------------------------------------------- **/

    // DrugstoreSupply
    if (
      ($resourceClass === DrugstoreSupply::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
      else {
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
      }
    }
    // End DrugstoreSupply

    /** ----------------------------------------------------------------------------------- **/

    // MedicineInvoice
    if (
      ($resourceClass === MedicineInvoice::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
      else {
        $qb->andWhere("$alias.user = :user");
        $qb->setParameter('user', $user);
      }
    }
    // End MedicineInvoice

    /** ----------------------------------------------------------------------------------- **/

    // Invoice
    if (
      ($resourceClass === Invoice::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->andWhere("$alias.isDeleted = :isDeleted")
          ->setParameter('isDeleted', false)
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Invoice

    /** ----------------------------------------------------------------------------------- **/

    // Consultation
    if (
      ($resourceClass === Consultation::class) &&
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
    // End Consultation

    /** ----------------------------------------------------------------------------------- **/

    // Appointment
    if (
      ($resourceClass === Appointment::class) &&
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
    // End Appointment

    /** ----------------------------------------------------------------------------------- **/

    // Lab
    if (
      ($resourceClass === Lab::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Lab

    /** ----------------------------------------------------------------------------------- **/

    // Prescription
    if (
      ($resourceClass === Prescription::class) &&
      !$this->currentUser->getAuth()->isGranted('ROLE_SUPER_ADMIN') &&
      $user instanceof User) {
      $alias = $this->rootAlias($qb);
      if (null !== $user->getUId()) {
        $qb->join("$alias.hospital", 'h')
          ->andWhere("$alias.hospital = :hospital")
          ->setParameter('hospital', $user->getHospital() ?? $user->getHospitalCenter());
      }
    }
    // End Prescription

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
