<?php

namespace App\Controller\Web;

use App\Entity\ContactMessage;
use App\Entity\Project;
use App\Entity\ProjectCategory;
use App\Form\Type\ContactMessageFormType;
use App\Manager\MailerManager;
use App\Repository\ContactMessageRepository;
use App\Repository\ProjectCategoryRepository;
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
    public function projectsList(ProjectCategoryRepository $pcr, ProjectRepository $pr): Response
    {
        return $this->render('web/projects.html.twig', [
            'categories' => $pcr->getAllSortedByName(),
            'projects' => $pr->getActiveAndShowInFrontendSortedByPosition(),
        ]);
    }

    #[Route(
        path: [
            'ca' => '/projecte-mostra/titol-projecte',
            'es' => '/proyecto-muestra/titulo-proyecto',
            'en' => '/project-sample/sample-project',
        ],
        name: 'app_web_project_placeholder',
    )]
    public function projectPlaceholder(ProjectRepository $pr): Response
    {
        if (count($pr->getActiveAndShowInFrontendSortedByPosition()) !== 0) {
            throw $this->createNotFoundException();
        }

        return $this->render('web/project_detail_placeholder.html.twig');
    }

    #[Route(
        path: [
            'ca' => '/categoria-projecte/{slug}',
            'es' => '/categoria-proyecto/{slug}',
            'en' => '/project-category/{slug}',
        ],
        name: 'app_web_project_category_detail',
    )]
    public function projectCategoryDetail(ProjectCategoryRepository $pcr, ProjectRepository $pr, ProjectCategory $projectCategory): Response
    {
        return $this->render('web/project_category_detail.html.twig', [
            'categories' => $pcr->getAllSortedByName(),
            'selected_category' => $projectCategory,
            'projects' => $pr->getProjectsByCategory($projectCategory),
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
    public function projectDetail(ProjectRepository $pr, Project $project): Response
    {
        return $this->render('web/project_detail.html.twig', [
            'previous_project' => $pr->getPreviousProjectOf($project),
            'project' => $project,
            'following_project' => $pr->getFollowingProjectOf($project),
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
    public function contact(Request $request, ContactMessageRepository $cmr, MailerManager $mm): Response
    {
        $contactMessage = new ContactMessage();
        $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cmr->add($contactMessage, true);
            $mm->sendNewContactMessageFromNotificationToManager($contactMessage);
            $contactMessage = new ContactMessage();
            $form = $this->createForm(ContactMessageFormType::class, $contactMessage);
            $this->addFlash(
                'success',
                'frontend.flash.on_contact_message_submit_success'
            );
        }

        return $this->render('web/contact.html.twig', [
            'form' => $form,
        ]);
    }
}
