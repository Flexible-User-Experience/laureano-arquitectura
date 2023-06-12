<?php

namespace App\Controller\Web;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'app_web_homepage',
    )]
    public function homepage(): Response
    {
        return $this->render('web/homepage.html.twig');
    }

    #[Route(
        path: [
            'ca' => '/projectes',
            'es' => '/proyectos',
            'en' => '/projects',
        ],
        name: 'app_web_projects_list',
    )]
    public function projectsList(ProjectRepository $pr): Response
    {
        return $this->render('web/projects.html.twig', [
            'projects' => $pr->getActiveAndShowInFrontendSortedByPosition(),
        ]);
    }

    #[Route(
        path: [
            'ca' => '/projecte/{slug}',
            'es' => '/proyecto/{slug}',
            'en' => '/project/{slug}',
        ],
        name: 'app_web_project_detail',
    )]
    public function projectDetail(Project $project): Response
    {
        return $this->render('web/project_detail.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/contacte', name: 'app_web_contact')]
    #[Route(
        path: [
            'ca' => '/contacte',
            'es' => '/contacto',
            'en' => '/contact',
        ],
        name: 'app_web_contact',
    )]
    public function contact(): Response
    {
        return $this->render('web/contact.html.twig', [
            'form' => 'MainController :: contact',
        ]);
    }
}
