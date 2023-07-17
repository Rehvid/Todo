<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRoles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin');
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $admin->setRoles([UserRoles::ADMIN]);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'admin'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);


        $user = new User();
        $user->setEmail('user@user');
        $user->setFirstname('User');
        $user->setLastname('User');
        $user->setRoles([UserRoles::USER]);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'user'
        );
        $user->setPassword($hashedPassword);
        $manager->persist($user);

        for ($i = 0; $i < 25; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->email());
            $user->setFirstname($this->faker->firstName());
            $user->setLastname($this->faker->lastName());
            $user->setRoles([UserRoles::USER]);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $this->faker->password()
            );
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
        }

        $manager->flush();
    }
}
