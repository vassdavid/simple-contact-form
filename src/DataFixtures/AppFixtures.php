<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Constant\UserRole;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
 
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) { }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $password = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $user->setRoles([UserRole::ROLE_ADMIN]);
        $manager->persist($user);

        $manager->flush();
    }
}
