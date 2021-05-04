<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait Login
{
    public function createAuthorizedClient(KernelBrowser $client, int $type = 0)
    {
        $container = $client->getContainer();
        $session = $container->get('session');
        $option = ($type === 0 || $type === 2) ? ['id' => $type === 0 ? 1 : 2] : ['email' => 'admin@admin.fr'];
        $user = $client
            ->getContainer()
            ->get('doctrine')
            ->getRepository(User::class)
            ->findBy($option)[0];
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
