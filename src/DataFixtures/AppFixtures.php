<?php

namespace App\DataFixtures;

use App\Entity\Act;
use App\Entity\ActCategory;
use App\Entity\Agent;
use App\Entity\Appointment;
use App\Entity\Bed;
use App\Entity\Bedroom;
use App\Entity\BedroomCategory;
use App\Entity\Box;
use App\Entity\Consultation;
use App\Entity\ConsultationsType;
use App\Entity\Covenant;
use App\Entity\Department;
use App\Entity\Exam;
use App\Entity\ExamCategory;
use App\Entity\Hospital;
use App\Entity\Invoice;
use App\Entity\Office;
use App\Entity\Parameters;
use App\Entity\Patient;
use App\Entity\Provider;
use App\Entity\Service;
use App\Entity\Treatment;
use App\Entity\TreatmentCategory;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  public function __construct(private readonly UserPasswordHasherInterface $encoder)
  {
  }

  public function load(ObjectManager $manager): void
  {
    $faker = Factory::create('fr_FR');

    // ROLE_SUPER_ADMIN
    $root = (new User())
      ->setEmail('adi.life91@gmail.com')
      ->setTel('0843210565')
      ->setCreatedAt($faker->dateTime('-8 months'))
      ->setUsername('root')
      ->setPassword('root!')
      ->setRoles(['ROLE_OWNER_ADMIN']);
    $password = $this->encoder->hashPassword($root, $root->getPassword());
    $root->setPassword($password);
    $root->setUId(uniqid().$root->getUsername());

    $hospital = (new Hospital())
      ->setCreatedAt($root->getCreatedAt())
      ->setUser($root)
      ->setEmail($root->getEmail())
      ->setDenomination('Back Office Pro')
      ->setAddress($faker->address);

    $box = (new Box())
      ->setSum(0)
      ->setHospital($hospital)
      ->setOutputSum(0);

    $parameters = (new Parameters())
      ->setCurrency('$')
      ->setName("(US) United States of America ' $ '")
      ->setCode('US')
      ->setHospital($hospital)
      ->setCreatedAt($root->getCreatedAt());

    $currentDate = new DateTime();

    // Departments
    for ($d = 0; $d < 50; $d++) {
      $department = (new Department())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setName($faker->jobTitle);

      for ($s = 0; $s < mt_rand(0, 5); $s++) {
        $service = (new Service())
          ->setName($faker->jobTitle)
          ->setHospital($hospital)
          ->setCreatedAt($department->getCreatedAt())
          ->setDepartment($department);
        $manager->persist($service);

        for ($o = 0; $o < mt_rand(1, 10); $o++) {
          $office = (new Office())
            ->setHospital($hospital)
            ->setCreatedAt($service->getCreatedAt())
            ->setTitle($faker->jobTitle);
          $manager->persist($office);
        }
      }

      $manager->persist($department);
    }
    // End Departments

    // Patients
    for ($a = 0; $a < 10; $a++) {
      $agentName = $faker->name;
      $agentLastName = $faker->lastName;
      $agentFirstName = $faker->firstName;
      $agentUsername = $faker->userName;
      $agentFullName = trim($agentName . ' ' . $agentLastName . ' ' . $agentFirstName);

      $user = (new User())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setName($agentName.' '.$agentFirstName)
        ->setEmail($faker->email)
        ->setUser($root)
        ->setPassword('pass')
        ->setRoles([$faker->randomElement(['ROLE_DOCTOR', 'ROLE_MEDIC'])])
        ->setUsername($agentUsername)
        ->setUId($root->getUId())
        ->setTel($faker->phoneNumber)
        ->setHospitalCenter($hospital);
      $user->setPassword($this->encoder->hashPassword($user, $user->getPassword()));
      $manager->persist($user);

      $agent = (new Agent())
        ->setUserAccount($user)
        ->setCreatedAt($user->getCreatedAt())
        ->setHospital($hospital)
        ->setName($agentName)
        ->setUser($root)
        ->setFirstName($agentFirstName)
        ->setFullName($agentFullName)
        ->setSex($faker->randomElement(['H', 'F']))
        ->setLastName($agentLastName)
        ->setPhone($user->getTel())
        ->setEmail($user->getEmail());
      $manager->persist($agent);

      for ($p = 0; $p < 5; $p++) {
        $name = $faker->randomElement([$faker->name('male'), $faker->name('female')]);
        $lastName = $faker->randomElement([$faker->name('male'), $faker->name('female')]);
        $firstName = $faker->randomElement([$faker->firstNameMale, $faker->firstNameFemale]);
        $fullName = trim($name.' '.$lastName.' '.$firstName);

        $birthDate = $faker->dateTimeBetween('-50 years', '-2 years');
        $age = (int) ($currentDate->format('Y') - $birthDate->format('Y'));

        $patient = (new Patient())
          ->setHospital($hospital)
          ->setAddress($faker->address)
          ->setTel($faker->phoneNumber)
          ->setUser($root)
          ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
          ->setName($name)
          ->setLastName($lastName)
          ->setFirstName($firstName)
          ->setFullName($fullName)
          ->setBirthDate($birthDate)
          ->setAge($age)
          ->setEmail($faker->email)
          ->setBirthPlace($faker->city)
          ->setMaritalStatus($faker->randomElement(['single', 'married']))
          ->setNationality($faker->country)
          ->setSex($faker->randomElement(['M', 'F']));
        $manager->persist($patient);

        // Consultations
        for ($c = 0; $c < mt_rand(0, 2); $c++) {
          $file = (new ConsultationsType())
            ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
            ->setHospital($hospital)
            ->setPrice($faker->biasedNumberBetween(0.1, 45.00))
            ->setWording($faker->realTextBetween(5, 35));
          $manager->persist($file);

          for ($c2 = 0; $c2 < mt_rand(0, 2); $c2++) {
            $consult = (new Consultation())
              ->setFile($file)
              ->setDoctor($agent)
              ->setPatient($patient)
              ->setHospital($hospital)
              ->setUser($root)
              ->setCreatedAt($patient->getCreatedAt())
              ->setFullName($patient->getFullName())
              ->setNote($faker->realText(1500, 5))
              ->setComment($faker->realText(500, 5))
              ->setTemperature($faker->numberBetween(38, 45));
            $manager->persist($consult);

            $appointment = (new Appointment())
              ->setFullName($patient->getFullName())
              ->setDoctor($consult->getDoctor())
              ->setUser($root)
              ->setHospital($hospital)
              ->setPatient($patient)
              ->setConsultation($consult)
              ->setReason('Consultation')
              ->setAppointmentDate($faker->dateTimeBetween('-6 months', 'now'))
              ->setCreatedAt($consult->getCreatedAt())
              ->setDescription($faker->realText);
            $manager->persist($appointment);

            $invoice = (new Invoice())
              ->setConsultation($consult)
              ->setPatient($patient)
              ->setHospital($hospital)
              ->setUser($root)
              ->setFullName($patient->getFullName())
              ->setAmount($file->getPrice())
              ->setSubTotal($file->getPrice())
              ->setTotalAmount($file->getPrice())
              ->setLeftover($file->getPrice())
              ->setCurrency('$')
              ->setReleasedAt($consult->getCreatedAt());
            $manager->persist($invoice);
          }
        }
        // End Consultations
      }
    }
    // End Patients

    // Acts
    for ($c = 0; $c < 5; $c++) {
      $category = (new ActCategory())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setName($faker->realTextBetween(5, 35));

      for ($a = 0; $a < mt_rand(0, 5); $a++) {
        $act = (new Act())
          ->setWording($faker->realTextBetween(5, 35))
          ->setPrice($faker->numberBetween(0, 50))
          ->setHospital($hospital)
          ->setCreatedAt($category->getCreatedAt())
          ->setCategory($category);
        $manager->persist($act);
      }

      $manager->persist($category);
    }
    // End Acts

    // Exams
    for ($c = 0; $c < 5; $c++) {
      $category = (new ExamCategory())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setName($faker->realTextBetween(5, 35));

      for ($a = 0; $a < mt_rand(0, 5); $a++) {
        $exam = (new Exam())
          ->setWording($faker->realTextBetween(5, 35))
          ->setPrice($faker->numberBetween(0.1, 50))
          ->setHospital($hospital)
          ->setCreatedAt($category->getCreatedAt())
          ->setCategory($category);
        $manager->persist($exam);
      }

      $manager->persist($category);
    }
    // End Exams

    // Treatments
    for ($c = 0; $c < 5; $c++) {
      $category = (new TreatmentCategory())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setName($faker->realTextBetween(5, 35));

      for ($a = 0; $a < mt_rand(0, 5); $a++) {
        $treatment = (new Treatment())
          ->setWording($faker->realTextBetween(5, 35))
          ->setPrice($faker->numberBetween(0.1, 50))
          ->setHospital($hospital)
          ->setCreatedAt($category->getCreatedAt())
          ->setCategory($category);
        $manager->persist($treatment);
      }

      $manager->persist($category);
    }
    // End Treatments

    // Bedrooms
    $bedroomCategories = ['Normale', 'Vip', 'Standard'];
    foreach ($bedroomCategories as $bedroomCategory) {
      $category = (new BedroomCategory())
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setName($bedroomCategory);
      $manager->persist($category);

      for ($a = 0; $a < mt_rand(0, 5); $a++) {
        $bedroom = (new Bedroom())
          ->setNumber($faker->title)
          ->setHospital($hospital)
          ->setCreatedAt($category->getCreatedAt())
          ->setCategory($category);
        $manager->persist($bedroom);

        for ($b = 0; $b < mt_rand(1, 4); $b++) {
          $bed = (new Bed())
            ->setCreatedAt($bedroom->getCreatedAt())
            ->setHospital($hospital)
            ->setNumber($faker->title)
            ->setPrice($faker->numberBetween(5, 20))
            ->setCost($faker->numberBetween(1, 5))
            ->setBedroom($bedroom);
          $manager->persist($bed);
        }
      }

      $manager->persist($category);
    }
    // End Bedrooms

    // Conventions
    for ($c = 0; $c < 11; $c++) {
      $covenant = (new Covenant())
        ->setTel($faker->phoneNumber)
        ->setEmail($faker->companyEmail)
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setHospital($hospital)
        ->setAddress($faker->address)
        ->setDenomination($faker->company)
        ->setFocal($faker->name);
      $manager->persist($covenant);

      for ($p = 0; $p < mt_rand(10, 20); $p++) {
        $name = $faker->randomElement([$faker->name('male'), $faker->name('female')]);
        $lastName = $faker->randomElement([$faker->name('male'), $faker->name('female')]);
        $firstName = $faker->randomElement([$faker->firstNameMale, $faker->firstNameFemale]);
        $fullName = trim($name.' '.$lastName.' '.$firstName);
        $birthDate = $faker->dateTimeBetween('-50 years', '-2 years');
        $age = (int) ($currentDate->format('Y') - $birthDate->format('Y'));

        $patient = (new Patient())
          ->setHospital($hospital)
          ->setAddress($faker->address)
          ->setTel($faker->phoneNumber)
          ->setUser($root)
          ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
          ->setName($name)
          ->setLastName($lastName)
          ->setFirstName($firstName)
          ->setFullName($fullName)
          ->setBirthDate($birthDate)
          ->setCovenant($covenant)
          ->setAge($age)
          ->setEmail($faker->email)
          ->setBirthPlace($faker->city)
          ->setMaritalStatus($faker->randomElement(['single', 'married']))
          ->setNationality($faker->country)
          ->setSex($faker->randomElement(['M', 'F']));
        $manager->persist($patient);
      }
    }
    // End Conventions

    // Providers
    for ($p = 0; $p < 30; $p++) {
      $provider = (new Provider())
        ->setFocal($faker->name)
        ->setAddress($faker->address)
        ->setHospital($hospital)
        ->setCreatedAt($faker->dateTimeBetween('-6 months', 'now'))
        ->setEmail($faker->companyEmail)
        ->setTel($faker->phoneNumber)
        ->setUser($root)
        ->setWording($faker->company);
      $manager->persist($provider);
    }
    // End Providers

    $manager->persist($root);
    $manager->persist($hospital);
    $manager->persist($box);
    $manager->persist($parameters);

    $manager->flush();
  }
}
