<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\Repository\HospitalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HospitalRepository::class)]
#[ApiResource(
  types: ['https://schema.org/Hospital'],
  operations: [
    new GetCollection(),
    new Post(),
    new Get(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['hospital:read']],
  order: ['id' => 'DESC'],
)]
class Hospital
{
  use CreatedAtTrait, IsDeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    #[Assert\NotBlank(message: 'La dénomination doit être renseigné.')]
    #[Assert\Length(min: 2, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    private ?string $denomination = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: 'Ce champs doit contenir {{ limit }} caractères au maximum .')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $unitName = null;

    #[ORM\OneToOne(inversedBy: 'hospital', cascade: ['persist', 'remove'])]
    #[Groups(['hospital:read'])]
    #[Assert\NotBlank(message: 'L\'utilisateur doit être renseigné.')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'hospitalCenter', targetEntity: User::class)]
    private Collection $users;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(min: 9, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    #[Assert\Regex('#^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$#', message: 'Numéro de téléphone non valide.')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $tel = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Adresse invalide.')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'hospitals')]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?ImageObject $logo = null;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Patient::class)]
    private Collection $patients;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Parameters::class)]
    private Collection $parameters;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Covenant::class)]
    private Collection $covenants;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ImageObject::class)]
    private Collection $imageObjects;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxInput::class)]
    private Collection $boxInputs;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ExpenseCategory::class)]
    private Collection $expenseCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Department::class)]
    private Collection $departments;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Service::class)]
    private Collection $services;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Office::class)]
    private Collection $offices;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Agent::class)]
    private Collection $agents;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Box::class)]
    #[ORM\JoinColumn(referencedColumnName: 'id', unique: false)]
    private Collection $boxes;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ConsultationsType::class)]
    private Collection $consultationsTypes;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Act::class)]
    private Collection $acts;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Exam::class)]
    private Collection $exams;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Treatment::class)]
    private Collection $treatments;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ActCategory::class)]
    private Collection $actCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ExamCategory::class)]
    private Collection $examCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: TreatmentCategory::class)]
    private Collection $treatmentCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: BedroomCategory::class)]
    private Collection $bedroomCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Bedroom::class)]
    private Collection $bedrooms;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Bed::class)]
    private Collection $beds;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Medicine::class)]
    private Collection $medicines;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: ConsumptionUnit::class)]
    private Collection $consumptionUnits;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: MedicineSubCategories::class)]
    private Collection $medicineSubCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: MedicineCategories::class)]
    private Collection $medicineCategories;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Provider::class)]
    private Collection $providers;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: DrugstoreSupply::class)]
    private Collection $drugstoreSupplies;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: MedicineInvoice::class)]
    private Collection $medicineInvoices;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['hospital:read', 'user:read', 'param:read'])]
    private ?string $address = null;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Consultation::class)]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Hospitalization::class)]
    private Collection $hospitalizations;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Lab::class)]
    private Collection $labs;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Prescription::class)]
    private Collection $prescriptions;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Nursing::class)]
    private Collection $nursings;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: Activities::class)]
    private Collection $activities;

    #[ORM\OneToMany(mappedBy: 'hospital', targetEntity: CovenantInvoice::class)]
    private Collection $covenantInvoices;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->patients = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->covenants = new ArrayCollection();
        $this->imageObjects = new ArrayCollection();
        $this->boxInputs = new ArrayCollection();
        $this->boxOutputs = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
        $this->expenseCategories = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->services = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->boxes = new ArrayCollection();
        $this->consultationsTypes = new ArrayCollection();
        $this->acts = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->treatments = new ArrayCollection();
        $this->actCategories = new ArrayCollection();
        $this->examCategories = new ArrayCollection();
        $this->treatmentCategories = new ArrayCollection();
        $this->bedroomCategories = new ArrayCollection();
        $this->bedrooms = new ArrayCollection();
        $this->beds = new ArrayCollection();
        $this->medicines = new ArrayCollection();
        $this->consumptionUnits = new ArrayCollection();
        $this->medicineSubCategories = new ArrayCollection();
        $this->medicineCategories = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->drugstoreSupplies = new ArrayCollection();
        $this->medicineInvoices = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->hospitalizations = new ArrayCollection();
        $this->labs = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->nursings = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->covenantInvoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getUnitName(): ?string
    {
        return $this->unitName;
    }

    public function setUnitName(?string $unitName): self
    {
        $this->unitName = $unitName;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setHospitalCenter($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getHospitalCenter() === $this) {
                $user->setHospitalCenter(null);
            }
        }

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLogo(): ?ImageObject
    {
        return $this->logo;
    }

    public function setLogo(?ImageObject $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, Patient>
     */
    public function getPatients(): Collection
    {
        return $this->patients;
    }

    public function addPatient(Patient $patient): self
    {
        if (!$this->patients->contains($patient)) {
            $this->patients->add($patient);
            $patient->setHospital($this);
        }

        return $this;
    }

    public function removePatient(Patient $patient): self
    {
        if ($this->patients->removeElement($patient)) {
            // set the owning side to null (unless already changed)
            if ($patient->getHospital() === $this) {
                $patient->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parameters>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameters $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters->add($parameter);
            $parameter->setHospital($this);
        }

        return $this;
    }

    public function removeParameter(Parameters $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getHospital() === $this) {
                $parameter->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Covenant>
     */
    public function getCovenants(): Collection
    {
        return $this->covenants;
    }

    public function addCovenant(Covenant $covenant): self
    {
        if (!$this->covenants->contains($covenant)) {
            $this->covenants->add($covenant);
            $covenant->setHospital($this);
        }

        return $this;
    }

    public function removeCovenant(Covenant $covenant): self
    {
        if ($this->covenants->removeElement($covenant)) {
            // set the owning side to null (unless already changed)
            if ($covenant->getHospital() === $this) {
                $covenant->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ImageObject>
     */
    public function getImageObjects(): Collection
    {
        return $this->imageObjects;
    }

    public function addImageObject(ImageObject $imageObject): self
    {
        if (!$this->imageObjects->contains($imageObject)) {
            $this->imageObjects->add($imageObject);
            $imageObject->setHospital($this);
        }

        return $this;
    }

    public function removeImageObject(ImageObject $imageObject): self
    {
        if ($this->imageObjects->removeElement($imageObject)) {
            // set the owning side to null (unless already changed)
            if ($imageObject->getHospital() === $this) {
                $imageObject->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxInput>
     */
    public function getBoxInputs(): Collection
    {
        return $this->boxInputs;
    }

    public function addBoxInput(BoxInput $boxInput): self
    {
        if (!$this->boxInputs->contains($boxInput)) {
            $this->boxInputs->add($boxInput);
            $boxInput->setHospital($this);
        }

        return $this;
    }

    public function removeBoxInput(BoxInput $boxInput): self
    {
        if ($this->boxInputs->removeElement($boxInput)) {
            // set the owning side to null (unless already changed)
            if ($boxInput->getHospital() === $this) {
                $boxInput->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxOutput>
     */
    public function getBoxOutputs(): Collection
    {
        return $this->boxOutputs;
    }

    public function addBoxOutput(BoxOutput $boxOutput): self
    {
        if (!$this->boxOutputs->contains($boxOutput)) {
            $this->boxOutputs->add($boxOutput);
            $boxOutput->setHospital($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getHospital() === $this) {
                $boxOutput->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BoxExpense>
     */
    public function getBoxExpenses(): Collection
    {
        return $this->boxExpenses;
    }

    public function addBoxExpense(BoxExpense $boxExpense): self
    {
        if (!$this->boxExpenses->contains($boxExpense)) {
            $this->boxExpenses->add($boxExpense);
            $boxExpense->setHospital($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getHospital() === $this) {
                $boxExpense->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExpenseCategory>
     */
    public function getExpenseCategories(): Collection
    {
        return $this->expenseCategories;
    }

    public function addExpenseCategory(ExpenseCategory $expenseCategory): self
    {
        if (!$this->expenseCategories->contains($expenseCategory)) {
            $this->expenseCategories->add($expenseCategory);
            $expenseCategory->setHospital($this);
        }

        return $this;
    }

    public function removeExpenseCategory(ExpenseCategory $expenseCategory): self
    {
        if ($this->expenseCategories->removeElement($expenseCategory)) {
            // set the owning side to null (unless already changed)
            if ($expenseCategory->getHospital() === $this) {
                $expenseCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setHospital($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            // set the owning side to null (unless already changed)
            if ($department->getHospital() === $this) {
                $department->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setHospital($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getHospital() === $this) {
                $service->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Office>
     */
    public function getOffices(): Collection
    {
        return $this->offices;
    }

    public function addOffice(Office $office): self
    {
        if (!$this->offices->contains($office)) {
            $this->offices->add($office);
            $office->setHospital($this);
        }

        return $this;
    }

    public function removeOffice(Office $office): self
    {
        if ($this->offices->removeElement($office)) {
            // set the owning side to null (unless already changed)
            if ($office->getHospital() === $this) {
                $office->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Agent>
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents->add($agent);
            $agent->setHospital($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getHospital() === $this) {
                $agent->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Box>
     */
    public function getBoxes(): Collection
    {
        return $this->boxes;
    }

    public function addBox(Box $box): self
    {
        if (!$this->boxes->contains($box)) {
            $this->boxes->add($box);
            $box->setHospital($this);
        }

        return $this;
    }

    public function removeBox(Box $box): self
    {
        if ($this->boxes->removeElement($box)) {
            // set the owning side to null (unless already changed)
            if ($box->getHospital() === $this) {
                $box->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConsultationsType>
     */
    public function getConsultationsTypes(): Collection
    {
        return $this->consultationsTypes;
    }

    public function addConsultationsType(ConsultationsType $consultationsType): self
    {
        if (!$this->consultationsTypes->contains($consultationsType)) {
            $this->consultationsTypes->add($consultationsType);
            $consultationsType->setHospital($this);
        }

        return $this;
    }

    public function removeConsultationsType(ConsultationsType $consultationsType): self
    {
        if ($this->consultationsTypes->removeElement($consultationsType)) {
            // set the owning side to null (unless already changed)
            if ($consultationsType->getHospital() === $this) {
                $consultationsType->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Act>
     */
    public function getActs(): Collection
    {
        return $this->acts;
    }

    public function addAct(Act $act): self
    {
        if (!$this->acts->contains($act)) {
            $this->acts->add($act);
            $act->setHospital($this);
        }

        return $this;
    }

    public function removeAct(Act $act): self
    {
        if ($this->acts->removeElement($act)) {
            // set the owning side to null (unless already changed)
            if ($act->getHospital() === $this) {
                $act->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exam>
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams->add($exam);
            $exam->setHospital($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getHospital() === $this) {
                $exam->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    public function addTreatment(Treatment $treatment): self
    {
        if (!$this->treatments->contains($treatment)) {
            $this->treatments->add($treatment);
            $treatment->setHospital($this);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): self
    {
        if ($this->treatments->removeElement($treatment)) {
            // set the owning side to null (unless already changed)
            if ($treatment->getHospital() === $this) {
                $treatment->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ActCategory>
     */
    public function getActCategories(): Collection
    {
        return $this->actCategories;
    }

    public function addActCategory(ActCategory $actCategory): self
    {
        if (!$this->actCategories->contains($actCategory)) {
            $this->actCategories->add($actCategory);
            $actCategory->setHospital($this);
        }

        return $this;
    }

    public function removeActCategory(ActCategory $actCategory): self
    {
        if ($this->actCategories->removeElement($actCategory)) {
            // set the owning side to null (unless already changed)
            if ($actCategory->getHospital() === $this) {
                $actCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExamCategory>
     */
    public function getExamCategories(): Collection
    {
        return $this->examCategories;
    }

    public function addExamCategory(ExamCategory $examCategory): self
    {
        if (!$this->examCategories->contains($examCategory)) {
            $this->examCategories->add($examCategory);
            $examCategory->setHospital($this);
        }

        return $this;
    }

    public function removeExamCategory(ExamCategory $examCategory): self
    {
        if ($this->examCategories->removeElement($examCategory)) {
            // set the owning side to null (unless already changed)
            if ($examCategory->getHospital() === $this) {
                $examCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TreatmentCategory>
     */
    public function getTreatmentCategories(): Collection
    {
        return $this->treatmentCategories;
    }

    public function addTreatmentCategory(TreatmentCategory $treatmentCategory): self
    {
        if (!$this->treatmentCategories->contains($treatmentCategory)) {
            $this->treatmentCategories->add($treatmentCategory);
            $treatmentCategory->setHospital($this);
        }

        return $this;
    }

    public function removeTreatmentCategory(TreatmentCategory $treatmentCategory): self
    {
        if ($this->treatmentCategories->removeElement($treatmentCategory)) {
            // set the owning side to null (unless already changed)
            if ($treatmentCategory->getHospital() === $this) {
                $treatmentCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BedroomCategory>
     */
    public function getBedroomCategories(): Collection
    {
        return $this->bedroomCategories;
    }

    public function addBedroomCategory(BedroomCategory $bedroomCategory): self
    {
        if (!$this->bedroomCategories->contains($bedroomCategory)) {
            $this->bedroomCategories->add($bedroomCategory);
            $bedroomCategory->setHospital($this);
        }

        return $this;
    }

    public function removeBedroomCategory(BedroomCategory $bedroomCategory): self
    {
        if ($this->bedroomCategories->removeElement($bedroomCategory)) {
            // set the owning side to null (unless already changed)
            if ($bedroomCategory->getHospital() === $this) {
                $bedroomCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bedroom>
     */
    public function getBedrooms(): Collection
    {
        return $this->bedrooms;
    }

    public function addBedroom(Bedroom $bedroom): self
    {
        if (!$this->bedrooms->contains($bedroom)) {
            $this->bedrooms->add($bedroom);
            $bedroom->setHospital($this);
        }

        return $this;
    }

    public function removeBedroom(Bedroom $bedroom): self
    {
        if ($this->bedrooms->removeElement($bedroom)) {
            // set the owning side to null (unless already changed)
            if ($bedroom->getHospital() === $this) {
                $bedroom->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Bed>
     */
    public function getBeds(): Collection
    {
        return $this->beds;
    }

    public function addBed(Bed $bed): self
    {
        if (!$this->beds->contains($bed)) {
            $this->beds->add($bed);
            $bed->setHospital($this);
        }

        return $this;
    }

    public function removeBed(Bed $bed): self
    {
        if ($this->beds->removeElement($bed)) {
            // set the owning side to null (unless already changed)
            if ($bed->getHospital() === $this) {
                $bed->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Medicine>
     */
    public function getMedicines(): Collection
    {
        return $this->medicines;
    }

    public function addMedicine(Medicine $medicine): self
    {
        if (!$this->medicines->contains($medicine)) {
            $this->medicines->add($medicine);
            $medicine->setHospital($this);
        }

        return $this;
    }

    public function removeMedicine(Medicine $medicine): self
    {
        if ($this->medicines->removeElement($medicine)) {
            // set the owning side to null (unless already changed)
            if ($medicine->getHospital() === $this) {
                $medicine->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConsumptionUnit>
     */
    public function getConsumptionUnits(): Collection
    {
        return $this->consumptionUnits;
    }

    public function addConsumptionUnit(ConsumptionUnit $consumptionUnit): self
    {
        if (!$this->consumptionUnits->contains($consumptionUnit)) {
            $this->consumptionUnits->add($consumptionUnit);
            $consumptionUnit->setHospital($this);
        }

        return $this;
    }

    public function removeConsumptionUnit(ConsumptionUnit $consumptionUnit): self
    {
        if ($this->consumptionUnits->removeElement($consumptionUnit)) {
            // set the owning side to null (unless already changed)
            if ($consumptionUnit->getHospital() === $this) {
                $consumptionUnit->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedicineSubCategories>
     */
    public function getMedicineSubCategories(): Collection
    {
        return $this->medicineSubCategories;
    }

    public function addMedicineSubCategory(MedicineSubCategories $medicineSubCategory): self
    {
        if (!$this->medicineSubCategories->contains($medicineSubCategory)) {
            $this->medicineSubCategories->add($medicineSubCategory);
            $medicineSubCategory->setHospital($this);
        }

        return $this;
    }

    public function removeMedicineSubCategory(MedicineSubCategories $medicineSubCategory): self
    {
        if ($this->medicineSubCategories->removeElement($medicineSubCategory)) {
            // set the owning side to null (unless already changed)
            if ($medicineSubCategory->getHospital() === $this) {
                $medicineSubCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedicineCategories>
     */
    public function getMedicineCategories(): Collection
    {
        return $this->medicineCategories;
    }

    public function addMedicineCategory(MedicineCategories $medicineCategory): self
    {
        if (!$this->medicineCategories->contains($medicineCategory)) {
            $this->medicineCategories->add($medicineCategory);
            $medicineCategory->setHospital($this);
        }

        return $this;
    }

    public function removeMedicineCategory(MedicineCategories $medicineCategory): self
    {
        if ($this->medicineCategories->removeElement($medicineCategory)) {
            // set the owning side to null (unless already changed)
            if ($medicineCategory->getHospital() === $this) {
                $medicineCategory->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Provider>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setHospital($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getHospital() === $this) {
                $provider->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DrugstoreSupply>
     */
    public function getDrugstoreSupplies(): Collection
    {
        return $this->drugstoreSupplies;
    }

    public function addDrugstoreSupply(DrugstoreSupply $drugstoreSupply): self
    {
        if (!$this->drugstoreSupplies->contains($drugstoreSupply)) {
            $this->drugstoreSupplies->add($drugstoreSupply);
            $drugstoreSupply->setHospital($this);
        }

        return $this;
    }

    public function removeDrugstoreSupply(DrugstoreSupply $drugstoreSupply): self
    {
        if ($this->drugstoreSupplies->removeElement($drugstoreSupply)) {
            // set the owning side to null (unless already changed)
            if ($drugstoreSupply->getHospital() === $this) {
                $drugstoreSupply->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedicineInvoice>
     */
    public function getMedicineInvoices(): Collection
    {
        return $this->medicineInvoices;
    }

    public function addMedicineInvoice(MedicineInvoice $medicineInvoice): self
    {
        if (!$this->medicineInvoices->contains($medicineInvoice)) {
            $this->medicineInvoices->add($medicineInvoice);
            $medicineInvoice->setHospital($this);
        }

        return $this;
    }

    public function removeMedicineInvoice(MedicineInvoice $medicineInvoice): self
    {
        if ($this->medicineInvoices->removeElement($medicineInvoice)) {
            // set the owning side to null (unless already changed)
            if ($medicineInvoice->getHospital() === $this) {
                $medicineInvoice->setHospital(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setHospital($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getHospital() === $this) {
                $invoice->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): self
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setHospital($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getHospital() === $this) {
                $consultation->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments->add($appointment);
            $appointment->setHospital($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getHospital() === $this) {
                $appointment->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hospitalization>
     */
    public function getHospitalizations(): Collection
    {
        return $this->hospitalizations;
    }

    public function addHospitalization(Hospitalization $hospitalization): self
    {
        if (!$this->hospitalizations->contains($hospitalization)) {
            $this->hospitalizations->add($hospitalization);
            $hospitalization->setHospital($this);
        }

        return $this;
    }

    public function removeHospitalization(Hospitalization $hospitalization): self
    {
        if ($this->hospitalizations->removeElement($hospitalization)) {
            // set the owning side to null (unless already changed)
            if ($hospitalization->getHospital() === $this) {
                $hospitalization->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lab>
     */
    public function getLabs(): Collection
    {
        return $this->labs;
    }

    public function addLab(Lab $lab): self
    {
        if (!$this->labs->contains($lab)) {
            $this->labs->add($lab);
            $lab->setHospital($this);
        }

        return $this;
    }

    public function removeLab(Lab $lab): self
    {
        if ($this->labs->removeElement($lab)) {
            // set the owning side to null (unless already changed)
            if ($lab->getHospital() === $this) {
                $lab->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Prescription>
     */
    public function getPrescriptions(): Collection
    {
        return $this->prescriptions;
    }

    public function addPrescription(Prescription $prescription): self
    {
        if (!$this->prescriptions->contains($prescription)) {
            $this->prescriptions->add($prescription);
            $prescription->setHospital($this);
        }

        return $this;
    }

    public function removePrescription(Prescription $prescription): self
    {
        if ($this->prescriptions->removeElement($prescription)) {
            // set the owning side to null (unless already changed)
            if ($prescription->getHospital() === $this) {
                $prescription->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Nursing>
     */
    public function getNursings(): Collection
    {
        return $this->nursings;
    }

    public function addNursing(Nursing $nursing): self
    {
        if (!$this->nursings->contains($nursing)) {
            $this->nursings->add($nursing);
            $nursing->setHospital($this);
        }

        return $this;
    }

    public function removeNursing(Nursing $nursing): self
    {
        if ($this->nursings->removeElement($nursing)) {
            // set the owning side to null (unless already changed)
            if ($nursing->getHospital() === $this) {
                $nursing->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activities>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activities $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setHospital($this);
        }

        return $this;
    }

    public function removeActivity(Activities $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getHospital() === $this) {
                $activity->setHospital(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CovenantInvoice>
     */
    public function getCovenantInvoices(): Collection
    {
        return $this->covenantInvoices;
    }

    public function addCovenantInvoice(CovenantInvoice $covenantInvoice): self
    {
        if (!$this->covenantInvoices->contains($covenantInvoice)) {
            $this->covenantInvoices->add($covenantInvoice);
            $covenantInvoice->setHospital($this);
        }

        return $this;
    }

    public function removeCovenantInvoice(CovenantInvoice $covenantInvoice): self
    {
        if ($this->covenantInvoices->removeElement($covenantInvoice)) {
            // set the owning side to null (unless already changed)
            if ($covenantInvoice->getHospital() === $this) {
                $covenantInvoice->setHospital(null);
            }
        }

        return $this;
    }
}
