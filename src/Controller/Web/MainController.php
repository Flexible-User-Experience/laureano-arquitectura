<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_web_homepage')]
    public function homepage(): Response
    {
        return $this->render('web/homepage.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
