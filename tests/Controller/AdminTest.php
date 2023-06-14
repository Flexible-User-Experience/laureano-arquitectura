<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @dataProvider provideNotFoundUrls
     */
    public function testNotFoundPages(string $url): void
    {

        $client = static::getAdminAuthenticatedClient();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function provideNotFoundUrls(): array
    {
        return [
            ['/admin/web/contact-message/batch'],
        ];
    }

    private static function getAdminAuthenticatedClient(): KernelBrowser
    {
        return WebTestCase::createClient([], [
            'PHP_AUTH_USER' => 'admin@admin.com',
            'PHP_AUTH_PW'   => 'admin',
        ]);
    }
}
