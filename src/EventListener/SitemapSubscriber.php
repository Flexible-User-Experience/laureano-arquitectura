<?php

namespace App\EventListener;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\UrlContainerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapSubscriber implements EventSubscriberInterface
{
    private ProjectRepository $pr;

    public function __construct(ProjectRepository $pr)
    {
        $this->pr = $pr;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SitemapPopulateEvent::class => 'populate',
        ];
    }

    public function populate(SitemapPopulateEvent $event): void
    {
        $this->registerBlogPostsUrls($event->getUrlContainer(), $event->getUrlGenerator());
    }

    public function registerBlogPostsUrls(UrlContainerInterface $urls, UrlGeneratorInterface $router): void
    {
        $projects = $this->pr->getActiveAndShowInFrontendSortedByPosition();
        /** @var Project $project */
        foreach ($projects as $project) {
            $urls->addUrl(
                new UrlConcrete(
                    $router->generate(
                        'app_web_project_detail',
                        [
                            'slug' => $project->getSlug()
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                ),
                'default'
            );
        }
    }
}
