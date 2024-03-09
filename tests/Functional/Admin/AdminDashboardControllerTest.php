<?php

namespace App\Tests\Functional\Admin;

use App\Tests\Trait\GetUserTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminDashboardControllerTest extends WebTestCase
{
    use GetUserTrait;

    public function testAdminNoLogged(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin');

        static::assertResponseRedirects();
        $client->followRedirect();

        static::assertResponseIsSuccessful();
        static::assertRouteSame('app_login');
        static::assertSelectorTextContains('h1', 'Please sign in');
    }

    public function testAdminDashboard(): void
    {
        $client = static::createClient();
        $client->loginUser(self::getUser('admin@gmail.com'));
        $client->request('GET', '/admin');

        static::assertResponseIsSuccessful();
        static::assertSelectorTextContains('h1', 'Manage your formations');
    }
}
