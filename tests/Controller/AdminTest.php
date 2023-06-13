<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminTest extends WebTestCase
{
    public function testPublicPages(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');
        self::assertResponseIsSuccessful();
    }

    /**
     * @dataProvider provideSuccessfulUrls
     */
    public function testSuccessfulPages(string $url): void
    {

        $client = static::getAdminAuthenticatedClient();
        $client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function provideSuccessfulUrls(): array
    {
        return [
            ['/admin/dashboard'],
            ['/admin/web/project/list'],
            ['/admin/web/project/create'],
            ['/admin/web/project/1/edit'],
            ['/admin/web/project/1/delete'],
            ['/admin/web/contact-message/list'],
            ['/admin/web/contact-message/1/show'],
            ['/admin/web/contact-message/1/delete'],
        ];
    }

    private static function getAdminAuthenticatedClient(): KernelBrowser
    {
        return WebTestCase::createClient([], [
            'PHP_AUTH_USER' => 'superadmin',
            'PHP_AUTH_PW'   => 'superadmin',
        ]);
    }
}
