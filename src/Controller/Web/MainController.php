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
            'controller_name' => 'MainController :: homepage',
        ]);
    }

    #[Route('/projectes', name: 'app_web_projects_list')]
    public function projectsList(): Response
    {
        return $this->render('web/homepage.html.twig', [
            'controller_name' => 'MainController :: projectsList',
        ]);
    }

    #[Route('/projecte', name: 'app_web_project_detail')]
    public function projectDetail(): Response
    {
        return $this->render('web/homepage.html.twig', [
            'controller_name' => 'MainController :: projectDetail',
        ]);
    }

    #[Route('/contacte', name: 'app_web_contact')]
    public function contact(): Response
    {
        return $this->render('web/homepage.html.twig', [
            'controller_name' => 'MainController :: contact',
        ]);
    }
}
