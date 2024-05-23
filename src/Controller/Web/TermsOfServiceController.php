<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TermsOfServiceController extends AbstractController
{
    #[Route(
        path: [
            'ca' => '/politica-de-privacitat',
            'es' => '/politica-de-privacidad',
            'en' => '/privacy-policy',
        ],
        name: 'app_web_privacy_policy',
    )]
    public function projectsList(): Response
    {
        return $this->render('web/privacy_policy.html.twig');
    }

    #[Route(
        path: [
            'ca' => '/canviar-a-idioma/{language}',
            'es' => '/cambiar-a-idioma/{language}',
            'en' => '/change-to-language/{language}',
        ],
        name: 'app_web_change_to_language',
    )]
    public function changeToLanguage(string $language): RedirectResponse
    {
        return $this->redirectToRoute('app_web_homepage', [
            '_locale' => $language,
        ]);
    }
}
