<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  public function __construct(private readonly UserPasswordHasherInterface $encoder)
  {
  }

  public function load(ObjectManager $manager): void
  {
    // $product = new Product();
    // $manager->persist($product);
    $root = (new User())
      ->setEmail('adi.life91@gmail.com')
      ->setTel('0843210565')
      ->setCreatedAt(new \DateTime('now'))
      ->setUsername('root')
      ->setPassword('root!')
      ->setRoles(['ROLE_SUPER_ADMIN']);
    $password = $this->encoder->hashPassword($root, $root->getPassword());
    $root->setPassword($password);
    $manager->persist($root);
    // Persist of Default User (Root)

    $manager->flush();
  }
}
