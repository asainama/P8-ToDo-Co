<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Task;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @codeCoverageIgnore
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker::create('fr_FR');
        for ($i = 0; $i < 50; $i ++) {
            $task = new Task();
            /** @var User $user */
            $user = $this->getReference('user_'. random_int(0, 10));
            $task
                ->setTitle($faker->sentence(3))
                ->setContent($faker->text())
                ->toggle(($i % 2 == 0) ? true : false)
                ->setCreatedAt($faker->dateTime())
                ->setUser($user);
            $manager->persist($task);
        }

        $manager->flush();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}
