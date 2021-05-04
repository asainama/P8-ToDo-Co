<?php

namespace App\Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;
    /**
    * @var \Doctrine\ORM\EntityManager
    */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCount()
    {
        self::bootKernel();
        $this->loadFixtures([TaskFixtures::class]);
        $tasks = self::$container->get(TaskRepository::class)->count([]);
        $this->assertEquals(50, $tasks);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
