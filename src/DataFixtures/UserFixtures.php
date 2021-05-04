<?php

namespace App\DataFixtures;

use Faker\Factory as Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i ++) {
            $faker = Faker::create('fr_FR');
            $user = new User();
            $user
            ->setUsername($faker->username())
            ->setEmail($faker->email())
            ->setRoles(['ROLE_USER'])
            ->setPassword(
                $this->encoder->encodePassword(
                    $user,
                    'admin'
                )
            );
            $manager->persist($user);
            $this->addReference("user_$i", $user);
        }

        $user = new User();
        $user
        ->setUsername("admin")
        ->setEmail("admin@admin.fr")
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword(
            $this->encoder->encodePassword(
                $user,
                'admin'
            )
        );
        $manager->persist($user);
        $this->addReference("user_10", $user);
        $manager->flush();
    }
}
