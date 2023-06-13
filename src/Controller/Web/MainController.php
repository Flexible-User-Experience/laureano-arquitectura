<?php

namespace App\Controller\Web;

use App\Entity\ContactMessage;
use App\Entity\Project;
use App\Form\Type\ContactMessageFormType;
use App\Repository\ContactMessageRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function contact(Request $request, ContactMessageRepository $contactMessageRepository): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactMessageRepository->add($contactMessage, true);
// TODO           $mailerManager->sendNewContactMessageFromNotificationToManager($contactMessage);
            $contactMessage = new ContactMessage();
            $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
            $this->addFlash(
                'success',
                'frontend.contact.form.on_submit_success'
            );
        }

        return $this->render('web/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
