<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
            // ca
            ['/'],
            ['/projectes'],
            ['/contacte'],
            ['/politica-de-privacitat'],
            // es
            ['/es/'],
            ['/es/proyectos'],
            ['/es/contacto'],
            ['/es/politica-de-privacidad'],
            // en
            ['/en/'],
            ['/en/projects'],
            ['/en/contact'],
            ['/en/privacy-policy'],
        ];
    }
}
