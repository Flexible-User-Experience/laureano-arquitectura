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
    private array $locales;

    public function __construct(ProjectRepository $pr, array $locales)
    {
        $this->pr = $pr;
        $this->locales = $locales;
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
        // locales iterator
        foreach ($this->locales as $locale) {
            // static sections
            $urls->addUrl(
                $this->buildUrl($router, 'app_web_homepage', $locale),
                'default'
            );
            $urls->addUrl(
                $this->buildUrl($router, 'app_web_projects_list', $locale),
                'default'
            );
            // projects list
            $projects = $this->pr->getActiveAndShowInFrontendSortedByPosition();
            /** @var Project $project */
            foreach ($projects as $project) {
                $urls->addUrl(
                    $this->buildUrl($router, 'app_web_project_detail', $locale, [
                        'slug' => $project->getSlug(),
                    ]),
                    'default'
                );
            }
            // contact
            $urls->addUrl(
                $this->buildUrl($router, 'app_web_contact', $locale),
                'default'
            );
        }
    }

    private function buildUrl(UrlGeneratorInterface $router, string $route, string $locale, array $routeParameters = []): UrlConcrete
    {
        $routeParameters['_locale'] = $locale;

        return new UrlConcrete(
            $router->generate(
                $route,
                $routeParameters,
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }
}
