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
            ['/admin/web/project-category/list'],
            ['/admin/web/project-category/create'],
            ['/admin/web/project-category/1/edit'],
            ['/admin/web/project/list'],
            ['/admin/web/project/create'],
            ['/admin/web/project/1/edit'],
            ['/admin/web/project/1/delete'],
            ['/admin/web/project-image/list'],
            ['/admin/web/project-image/create'],
            ['/admin/web/project-image/1/edit'],
            ['/admin/web/project-image/1/delete'],
            ['/admin/web/contact-message/list'],
            ['/admin/web/contact-message/1/show'],
            ['/admin/web/contact-message/1/delete'],
            ['/admin/enterprise/provider/list'],
            ['/admin/enterprise/provider/create'],
            ['/admin/enterprise/provider/1/edit'],
            ['/admin/enterprise/expense-category/list'],
            ['/admin/enterprise/expense-category/create'],
            ['/admin/enterprise/expense-category/1/edit'],
            ['/admin/enterprise/expense/list'],
            ['/admin/enterprise/expense/create'],
            ['/admin/enterprise/expense/1/edit'],
            ['/admin/enterprise/expense/1/delete'],
            ['/admin/enterprise/customer/list'],
            ['/admin/enterprise/customer/create'],
            ['/admin/enterprise/customer/1/edit'],
            ['/admin/enterprise/invoice/list'],
            ['/admin/enterprise/invoice/create'],
            ['/admin/enterprise/invoice/1/edit'],
            ['/admin/enterprise/invoice-line/list'],
            ['/admin/enterprise/invoice-line/create'],
            ['/admin/enterprise/invoice-line/1/edit'],
            ['/admin/enterprise/invoice-line/1/show'],
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
            ['/admin/web/project-category/batch'],
            ['/admin/web/project-category/1/show'],
            ['/admin/web/project-category/1/delete'],
            ['/admin/web/project/batch'],
            ['/admin/web/project/1/show'],
            ['/admin/web/project-image/batch'],
            ['/admin/web/project-image/1/show'],
            ['/admin/web/contact-message/batch'],
            ['/admin/web/contact-message/1/edit'],
            ['/admin/enterprise/provider/100/edit'],
            ['/admin/enterprise/provider/1/show'],
            ['/admin/enterprise/provider/1/delete'],
            ['/admin/enterprise/provider/batch'],
            ['/admin/enterprise/expense-category/100/edit'],
            ['/admin/enterprise/expense-category/1/show'],
            ['/admin/enterprise/expense-category/1/delete'],
            ['/admin/enterprise/expense-category/batch'],
            ['/admin/enterprise/expense/100/edit'],
            ['/admin/enterprise/expense/1/show'],
            ['/admin/enterprise/expense/batch'],
            ['/admin/enterprise/customer/100/edit'],
            ['/admin/enterprise/customer/1/show'],
            ['/admin/enterprise/customer/1/delete'],
            ['/admin/enterprise/customer/batch'],
            ['/admin/enterprise/invoice/100/edit'],
            ['/admin/enterprise/invoice/1/show'],
            ['/admin/enterprise/invoice/1/delete'],
            ['/admin/enterprise/invoice/batch'],
            ['/admin/enterprise/invoice-line/100/edit'],
            ['/admin/enterprise/invoice-line/1/delete'],
            ['/admin/enterprise/invoice-line/batch'],
        ];
    }

    /**
     * @dataProvider provideForbiddenUrls
     */
    public function testForbiddenPages(string $url): void
    {

        $client = static::getAdminAuthenticatedClient();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function provideForbiddenUrls(): array
    {
        return [
            ['/admin/app/user/list'],
            ['/admin/app/user/create'],
            ['/admin/app/user/1/edit'],
            ['/admin/app/user/1/delete'],
            ['/admin/app/user/1/show'],
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
