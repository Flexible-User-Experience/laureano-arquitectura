<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class WebTest extends WebTestCase
{
    /**
     * @dataProvider provideSuccessfulUrls
     */
    public function testSuccessfulPages(string $url): void
    {

        $client = static::createClient();
        $client->request('GET', $url);
        self::assertResponseIsSuccessful();
    }

    public function provideSuccessfulUrls(): array
    {
        return [
            // sitemap
            ['/sitemap.default.xml'],
            // ca
            ['/'],
            ['/categoria-projecte/project-category-1'],
            ['/projectes'],
            ['/projecte/project-1'],
            ['/contacte'],
            ['/politica-de-privacitat'],
            // es
            ['/es/'],
            ['/es/categoria-proyecto/project-category-1'],
            ['/es/proyectos'],
            ['/es/proyecto/project-1'],
            ['/es/contacto'],
            ['/es/politica-de-privacidad'],
            // en
            ['/en/'],
            ['/en/project-category/project-category-1'],
            ['/en/projects'],
            ['/en/project/project-1'],
            ['/en/contact'],
            ['/en/privacy-policy'],
        ];
    }

    /**
     * @dataProvider provideNotFoundUrls
     */
    public function testNotFoundPages(string $url): void
    {

        $client = static::createClient();
        $client->request('GET', $url);
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function provideNotFoundUrls(): array
    {
        return [
            ['/not-found-url'],
        ];
    }
}
