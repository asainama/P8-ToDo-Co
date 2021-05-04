<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Login;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityControllerTest extends WebTestCase
{
    use Login;

    public function testDisplayLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('button', 'Se connecter');
    }

    public function testLogout()
    {
        $client = static::createClient();
        $client = $this->createAuthorizedClient($client);
        $client->request('GET', '/logout');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    // public function testLoginCheck()
    // {
    //     $client = $this->createAuthorizedClient();
    //     $client->request('GET', '/login_check');
    //     $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    // }

    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        // $csrfToken =  $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $crawler = $client->request('GET', '/login');
        $form = $crawler
                ->selectButton('Se connecter')
                ->form([
                    '_username' => 'test@test.fr',
                    '_password' => 'adeae',
                    // '_csrf_token' => $csrfToken
                ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testSucessfullLogin()
    {
        $client = static::createClient();
        // $csrfToken = $client->getContainer()->get('security.csrf.token_manager')->getToken('authenticate');
        $client->request('POST', '/login_check', [
            '_username' => 'admin@admin.fr',
            '_password' => 'admin',
            // '_csrf_token' => $csrfToken
        ]);
        $this->assertResponseRedirects('http://localhost/');
    }
}
