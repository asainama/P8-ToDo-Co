<?php

namespace App\Tests\Controller;

use App\Tests\Login;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use Login;
    public function testListUsersRedirect()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('http://localhost/login');
    }

    public function testShouldDisplayListUsers()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client, 1);
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUnAuthorizedCreateUser()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizedCreateUser()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client, 1);
        $crawler = $client->request('GET', '/users/create');
        $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('user');
        $form = $crawler->selectButton('Ajouter')->form();

        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(2, $crawler->filter('input[name="user[roles][]"]')->count());

        $form['user[username]'] = 'user' .random_int(10, 999);
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'user' .random_int(10, 999) . '@gmail.fr';
        $form['user[_token]'] = $csrfToken;
        $form['user[roles][0]']->tick();
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testUnAuthorizedEditUser()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $client->request('GET', '/users/1/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAuthorizedEditUser()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client, 1);

        // $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('user');
        $crawler = $client->request('GET', '/users/1/edit');
        $form = $crawler->selectButton('Modifier')->form();

        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(2, $crawler->filter('input[name="user[roles][]"]')->count());

        $form['user[password][first]'] = 'admin';
        $form['user[password][second]'] = 'admin';
        // $form['user[_token]'] = $csrfToken;

        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
