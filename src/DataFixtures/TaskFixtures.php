<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use App\Enum\Priority;
use App\Enum\Status;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class TaskFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);

        for ($i = 0; $i < 25; $i++) {
            $task = new Task();
            $task
                ->setTitle($this->faker->word())
                ->setDescription($this->faker->paragraph(6))
                ->setStatus($this->faker->randomElement(Status::cases()))
                ->setDeadline($this->faker->dateTimeBetween('-12 months', '+ 12 months'))
                ->setUser(
                    $this->getReference('user-'. $this->faker->numberBetween(0, 24))
                )
                ->setPriority($this->faker->randomElement(Priority::cases()))
            ;

            $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
