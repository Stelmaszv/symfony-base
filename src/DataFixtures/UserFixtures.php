<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->passwordEncoder = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $hashedPassword = $this->passwordEncoder->hashPassword(
            $user,
            123
        );
        $user->setRoles([]);
        $user->setId(Uuid::v4());
        $user->setEmail('user@qwe.com');
        $user->setPassword($hashedPassword);

        $manager->persist($user);
        $manager->flush();
    }
}