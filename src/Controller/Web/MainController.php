<?php

namespace App\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/web/main', name: 'app_web_main')]
    public function index(): Response
    {
        return $this->render('web/main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
