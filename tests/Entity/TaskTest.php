<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function getEntity()
    {
        $user = (new User())
        ->setUsername('Anonymous')
        ->setRoles(array('ROLE_USER'))
        ->setPassword('admin')
        ->setEmail('a@a.fr');

        $encoded = self::bootKernel()->getContainer()->get('security.password_encoder')
        ->encodePassword($user, 'password');
        $user->setPassword($encoded);

        return (new Task())
        ->setTitle('Nouveau test')
        ->setContent('Lorem, ipsum dolor sit amet consectetur adipisicing elit. Labore, inventore.')
        ->setCreatedAt(new DateTime())
        ->toggle(true)
        ->setUser($user);
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }
    public function assertHasErrors(Task $task, int $number = 0)
    {
        $errors = $this->validator->validate($task);
        $messages = [];
        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . '=>' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertIsBool($this->getEntity()->isDone());
        $this->assertInstanceOf(DateTime::class, $this->getEntity()->getCreatedAt());
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidBlankTitle()
    {
        $this->assertHasErrors($this->getEntity()->setTitle(''), 1);
    }

    public function testInvalidBlankContent()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
    }
}
