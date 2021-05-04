<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Tests\Login;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use Login;

    /** @var int $lastInsertId */
    private $lastInsertId;
    public function testAuthenticatedUserAccessTasks()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUnAutorizedTasksListAccess()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');
        $this->assertResponseRedirects('http://localhost/login');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testUnAutorizedCreateTaskAccess()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');
        $this->assertResponseRedirects('http://localhost/login');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testShouldAccesCreateTask()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $client->request('GET', '/tasks/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSubmitValidFormCreateTask()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('task');
        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler
                ->selectButton('Ajouter')
                ->form([
                    'task[title]' => 'Nouvelle tache',
                    'task[content]' => 'adeae',
                    'task[_token]' => $csrfToken
                ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSumbitValidFormTaskEdit()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/edit";
        $client = $this->createAuthorizedClient($client, 1);
        $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('task');
        $crawler = $client->request('GET', $url);
        $form = $crawler->selectButton('Modifier')->form();
        $form = $crawler
                ->selectButton('Modifier')
                ->form([
                    'task[title]' => 'Modification de la tache',
                    'task[content]' => 'tache modifié',
                    'task[_token]' => $csrfToken
                ]);
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="task[_token]"]')->count());
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSumbitInvalidUserFormTaskEdit()
    {
        $client = static::createClient();
        $url = '/tasks/' . (string) $this->lastTaskInserID($client) . '/edit';
        $client = $this->createAuthorizedClient($client, 2);
        $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('task');
        $crawler = $client->request('GET', $url);
        $form = $crawler
                ->selectButton('Modifier')
                ->form([
                    'task[title]' => 'Modification de la tache',
                    'task[content]' => 'tache modifiée',
                    'task[_token]' => $csrfToken
                ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSumbitInvalidUserFormTaskToggle()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/toggle";
        $client = $this->createAuthorizedClient($client, 2);
        $client->request('GET', $url);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSumbitInvalidUserFormTaskDelete()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/delete";
        $client = $this->createAuthorizedClient($client, 2);
        $client->request('GET', $url);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testShouldSumbitValidUserFormTaskToggle()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/toggle";
        $client = $this->createAuthorizedClient($client, 1);
        $client->request('GET', $url);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testUnAutorizedEditTaskAccess()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/edit";
        $client->request('GET', $url);
        $this->assertResponseRedirects('http://localhost/login');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testUnAutorizedDeleteTaskAccess()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/delete";
        $client->request('GET', $url);
        $this->assertResponseRedirects('http://localhost/login');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testUnAutorizedToggleTaskAccess()
    {
        $client = static::createClient();
        $url = "/tasks/" .(string) $this->lastTaskInserID($client)."/toggle";
        $client->request('GET', $url);
        $this->assertResponseRedirects('http://localhost/login');
        $this->assertResponseStatusCodeSame(302);
    }


    public function testShouldSumbitValidUserFormTaskDelete()
    {
        $client = static::createClient();
        $url = "/tasks/".(string) $this->lastTaskInserID($client)."/delete";
        $client = $this->createAuthorizedClient($client, 1);
        $client->request('GET', $url);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function lastTaskInserID($client): ?int
    {
        return $client
        ->getContainer()
        ->get('doctrine')
        ->getRepository(Task::class)
        ->findBy([], ['id' => 'desc'])[0]->getId();
    }
}
