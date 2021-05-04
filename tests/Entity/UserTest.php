<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function getEntity(): User
    {
        $user = (new User())
        ->setUsername('test')
        ->setEmail('test@test.fr')
        ->setRoles(array('ROLE_USER'));
        $encoded = self::$container->get('security.password_encoder')
        ->encodePassword($user, 'password');
        $user->setPassword($encoded);
        return $user;
    }
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
        $this->assertEmpty($this->getEntity()->getTasks());
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('aaaa'), 1);
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
        $this->assertHasErrors($this->getEntity()->setEmail('a@fr'), 1);
    }

    public function testInvalidUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }

    public function testValidPassword()
    {
        $user = $this->getEntity();
        $this->assertTrue(self::$container->get('security.password_encoder')->isPasswordValid($user, 'password'));
    }

    public function testInvalidPassword()
    {
        $user = $this->getEntity();
        $this->assertFalse(self::$container->get('security.password_encoder')->isPasswordValid($user, 'admin'));
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        $errors = $this->validator->validate($user);
        $messages = [];
        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . '=>' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testAddInvalidTaskToUser()
    {
        $task = (new Task())
            ->setTitle('')
            ->setCreatedAt(new DateTime())
            ->toggle(false)
            ->setContent('Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores, earum?');
        $errors = $this->validator->validate($task);
        $this->assertCount(1, $errors);
        $user = $this
                ->getEntity()
                ->addTask($task);
        $this->assertNotEmpty($user->getTasks());
        $this->assertCount(1, $user->getTasks());
    }

    public function testAddAndRemoveValidTaskToUser()
    {
        $task = (new Task())
            ->setTitle('Nouveau test')
            ->setCreatedAt(new DateTime())
            ->toggle(false)
            ->setContent('Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores, earum?');
        $user = $this
                ->getEntity()
                ->addTask($task);
        $this->assertNotEmpty($user->getTasks());
        $this->assertCount(1, $user->getTasks());

        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());
        $this->assertCount(0, $user->getTasks());
    }

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->validator = $kernel->getContainer()->get('validator');
    }
}
