<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\AppTraits\CreatedAtTrait;
use App\AppTraits\IsDeletedTrait;
use App\AppTraits\UIDTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
  types: ['https://schema.org/User'],
  operations: [
    new Get(),
    new Post(),
    new GetCollection(),
    new Patch(),
  ],
  normalizationContext: ['groups' => ['user:read']],
  order: ['id' => 'DESC']
)]
#[ApiFilter(SearchFilter::class, properties: ['username' => 'ipartial'])]
#[UniqueEntity('username', message: 'Ce username est déjà pris.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  use CreatedAtTrait, UIDTrait, IsDeletedTrait;

    public ?int $agentId = null;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
      'user:read',
      'activity:read',
      'patient:read',
      'provider:read',
      'hospital:read',
      'agent:read',
      'expense:read',
      'output:read',
      'input:read',
      'medicine:read',
      'supply:read',
      'medicineInvoice:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
      'destocking:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups([
      'user:read',
      'patient:read',
      'provider:read',
      'hospital:read',
      'agent:read',
      'expense:read',
      'output:read',
      'input:read',
      'medicine:read',
      'supply:read',
      'medicineInvoice:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'activity:read',
      'invoice:read',
      'destocking:read',
    ])]
    #[Assert\NotBlank(message: 'Le username doit être renseigné.')]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string
     * The hashed password
     */
    #[ORM\Column(nullable: false)]
    #[Assert\NotBlank(message: 'Le mot de passe doit être renseigné.')]
    #[Assert\Length(min: 4, minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères.')]
    private string $password;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'users')]
    #[Groups(['user:read', 'patient:read'])]
    private ?self $user = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: self::class)]
    private Collection $users;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read'])]
    #[Assert\Email(message: 'Adresse email invalide.')]
    private ?string $email = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['user:read'])]
    private ?Hospital $hospital = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read'])]
    private ?Hospital $hospitalCenter = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le numéro de téléphone doit être renseigné')]
    #[Assert\Length(min: 9, minMessage: 'Ce champs doit faire au moins {{ limit }} caractères.')]
    #[Groups(['user:read'])]
    private ?string $tel = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxInput::class)]
    private Collection $boxInputs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxOutput::class)]
    private Collection $boxOutputs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BoxExpense::class)]
    private Collection $boxExpenses;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[Groups(['user:read', 'agent:read'])]
    private ?PersonalImageObject $profile = null;

    public ?bool $isChangingPassword = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Agent::class)]
    private Collection $agents;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
      'user:read',
      'patient:read',
      'provider:read',
      'hospital:read',
      'agent:read',
      'expense:read',
      'output:read',
      'input:read',
      'medicine:read',
      'supply:read',
      'medicineInvoice:read',
      'consult:read',
      'lab:read',
      'prescript:read',
      'nursing:read',
      'appointment:read',
      'invoice:read',
      'destocking:read',
    ])]
    private ?string $name = null;

    #[ORM\OneToOne(mappedBy: 'userAccount', cascade: ['persist', 'remove'])]
    #[Groups(['user:read', 'medicine:read', 'lab:read'])]
    private ?Agent $agent = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Medicine::class)]
    private Collection $medicines;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Provider::class)]
    private Collection $providers;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DrugstoreSupply::class)]
    private Collection $drugstoreSupplies;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MedicineInvoice::class)]
    private Collection $medicineInvoices;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Consultation::class)]
    private Collection $consultations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: InvoiceStoric::class)]
    private Collection $invoiceStorics;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Appointment::class)]
    private Collection $appointments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Lab::class)]
    private Collection $labs;

    #[ORM\OneToMany(mappedBy: 'assistant', targetEntity: Lab::class)]
    private Collection $labAssistants;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Prescription::class)]
    private Collection $prescriptions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: NursingTreatment::class)]
    private Collection $nursingTreatments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Activities::class)]
    private Collection $activities;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CovenantInvoice::class)]
    private Collection $covenantInvoices;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: DestockingOfMedicines::class)]
    private Collection $destockingOfMedicines;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->boxInputs = new ArrayCollection();
        $this->boxOutputs = new ArrayCollection();
        $this->boxExpenses = new ArrayCollection();
        $this->agents = new ArrayCollection();
        $this->medicines = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->drugstoreSupplies = new ArrayCollection();
        $this->medicineInvoices = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->invoiceStorics = new ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->labs = new ArrayCollection();
        $this->labAssistants = new ArrayCollection();
        $this->prescriptions = new ArrayCollection();
        $this->nursingTreatments = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->covenantInvoices = new ArrayCollection();
        $this->destockingOfMedicines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUser(): ?self
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUser($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUser() === $this) {
                $user->setUser(null);
            }
        }

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

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        // unset the owning side of the relation if necessary
        if ($hospital === null && $this->hospital !== null) {
            $this->hospital->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($hospital !== null && $hospital->getUser() !== $this) {
            $hospital->setUser($this);
        }

        $this->hospital = $hospital;

        return $this;
    }

    public function getHospitalCenter(): ?Hospital
    {
        return $this->hospitalCenter;
    }

    public function setHospitalCenter(?Hospital $hospitalCenter): self
    {
        $this->hospitalCenter = $hospitalCenter;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
            $boxInput->setUser($this);
        }

        return $this;
    }

    public function removeBoxInput(BoxInput $boxInput): self
    {
        if ($this->boxInputs->removeElement($boxInput)) {
            // set the owning side to null (unless already changed)
            if ($boxInput->getUser() === $this) {
                $boxInput->setUser(null);
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
            $boxOutput->setUser($this);
        }

        return $this;
    }

    public function removeBoxOutput(BoxOutput $boxOutput): self
    {
        if ($this->boxOutputs->removeElement($boxOutput)) {
            // set the owning side to null (unless already changed)
            if ($boxOutput->getUser() === $this) {
                $boxOutput->setUser(null);
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
            $boxExpense->setUser($this);
        }

        return $this;
    }

    public function removeBoxExpense(BoxExpense $boxExpense): self
    {
        if ($this->boxExpenses->removeElement($boxExpense)) {
            // set the owning side to null (unless already changed)
            if ($boxExpense->getUser() === $this) {
                $boxExpense->setUser(null);
            }
        }

        return $this;
    }

    public function getProfile(): ?PersonalImageObject
    {
        return $this->profile;
    }

    public function setProfile(?PersonalImageObject $profile): self
    {
        $this->profile = $profile;

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
            $agent->setUser($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getUser() === $this) {
                $agent->setUser(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        // unset the owning side of the relation if necessary
        if ($agent === null && $this->agent !== null) {
            $this->agent->setUserAccount(null);
        }

        // set the owning side of the relation if necessary
        if ($agent !== null && $agent->getUserAccount() !== $this) {
            $agent->setUserAccount($this);
        }

        $this->agent = $agent;

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
            $medicine->setUser($this);
        }

        return $this;
    }

    public function removeMedicine(Medicine $medicine): self
    {
        if ($this->medicines->removeElement($medicine)) {
            // set the owning side to null (unless already changed)
            if ($medicine->getUser() === $this) {
                $medicine->setUser(null);
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
            $provider->setUser($this);
        }

        return $this;
    }

    public function removeProvider(Provider $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getUser() === $this) {
                $provider->setUser(null);
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
            $drugstoreSupply->setUser($this);
        }

        return $this;
    }

    public function removeDrugstoreSupply(DrugstoreSupply $drugstoreSupply): self
    {
        if ($this->drugstoreSupplies->removeElement($drugstoreSupply)) {
            // set the owning side to null (unless already changed)
            if ($drugstoreSupply->getUser() === $this) {
                $drugstoreSupply->setUser(null);
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
            $medicineInvoice->setUser($this);
        }

        return $this;
    }

    public function removeMedicineInvoice(MedicineInvoice $medicineInvoice): self
    {
        if ($this->medicineInvoices->removeElement($medicineInvoice)) {
            // set the owning side to null (unless already changed)
            if ($medicineInvoice->getUser() === $this) {
                $medicineInvoice->setUser(null);
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
            $consultation->setUser($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): self
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getUser() === $this) {
                $consultation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvoiceStoric>
     */
    public function getInvoiceStorics(): Collection
    {
        return $this->invoiceStorics;
    }

    public function addInvoiceStoric(InvoiceStoric $invoiceStoric): self
    {
        if (!$this->invoiceStorics->contains($invoiceStoric)) {
            $this->invoiceStorics->add($invoiceStoric);
            $invoiceStoric->setUser($this);
        }

        return $this;
    }

    public function removeInvoiceStoric(InvoiceStoric $invoiceStoric): self
    {
        if ($this->invoiceStorics->removeElement($invoiceStoric)) {
            // set the owning side to null (unless already changed)
            if ($invoiceStoric->getUser() === $this) {
                $invoiceStoric->setUser(null);
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
            $appointment->setUser($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getUser() === $this) {
                $appointment->setUser(null);
            }
        }

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
            $invoice->setUser($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getUser() === $this) {
                $invoice->setUser(null);
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
            $lab->setUser($this);
        }

        return $this;
    }

    public function removeLab(Lab $lab): self
    {
        if ($this->labs->removeElement($lab)) {
            // set the owning side to null (unless already changed)
            if ($lab->getUser() === $this) {
                $lab->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lab>
     */
    public function getLabAssistants(): Collection
    {
        return $this->labAssistants;
    }

    public function addLabAssistant(Lab $labAssistant): self
    {
        if (!$this->labAssistants->contains($labAssistant)) {
            $this->labAssistants->add($labAssistant);
            $labAssistant->setAssistant($this);
        }

        return $this;
    }

    public function removeLabAssistant(Lab $labAssistant): self
    {
        if ($this->labAssistants->removeElement($labAssistant)) {
            // set the owning side to null (unless already changed)
            if ($labAssistant->getAssistant() === $this) {
                $labAssistant->setAssistant(null);
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
            $prescription->setUser($this);
        }

        return $this;
    }

    public function removePrescription(Prescription $prescription): self
    {
        if ($this->prescriptions->removeElement($prescription)) {
            // set the owning side to null (unless already changed)
            if ($prescription->getUser() === $this) {
                $prescription->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NursingTreatment>
     */
    public function getNursingTreatments(): Collection
    {
        return $this->nursingTreatments;
    }

    public function addNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if (!$this->nursingTreatments->contains($nursingTreatment)) {
            $this->nursingTreatments->add($nursingTreatment);
            $nursingTreatment->setUser($this);
        }

        return $this;
    }

    public function removeNursingTreatment(NursingTreatment $nursingTreatment): self
    {
        if ($this->nursingTreatments->removeElement($nursingTreatment)) {
            // set the owning side to null (unless already changed)
            if ($nursingTreatment->getUser() === $this) {
                $nursingTreatment->setUser(null);
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
            $activity->setAuthor($this);
        }

        return $this;
    }

    public function removeActivity(Activities $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getAuthor() === $this) {
                $activity->setAuthor(null);
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
            $covenantInvoice->setUser($this);
        }

        return $this;
    }

    public function removeCovenantInvoice(CovenantInvoice $covenantInvoice): self
    {
        if ($this->covenantInvoices->removeElement($covenantInvoice)) {
            // set the owning side to null (unless already changed)
            if ($covenantInvoice->getUser() === $this) {
                $covenantInvoice->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DestockingOfMedicines>
     */
    public function getDestockingOfMedicines(): Collection
    {
        return $this->destockingOfMedicines;
    }

    public function addDestockingOfMedicine(DestockingOfMedicines $destockingOfMedicine): self
    {
        if (!$this->destockingOfMedicines->contains($destockingOfMedicine)) {
            $this->destockingOfMedicines->add($destockingOfMedicine);
            $destockingOfMedicine->setUser($this);
        }

        return $this;
    }

    public function removeDestockingOfMedicine(DestockingOfMedicines $destockingOfMedicine): self
    {
        if ($this->destockingOfMedicines->removeElement($destockingOfMedicine)) {
            // set the owning side to null (unless already changed)
            if ($destockingOfMedicine->getUser() === $this) {
                $destockingOfMedicine->setUser(null);
            }
        }

        return $this;
    }
}
