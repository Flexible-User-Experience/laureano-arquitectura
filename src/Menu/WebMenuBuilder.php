<?php

namespace App\Menu;

use App\Enum\LanguageEnum;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class WebMenuBuilder
{
    private FactoryInterface $factory;
    private RequestStack $requestStack;

    public function __construct(FactoryInterface $factory, RequestStack $requestStack)
    {
        $this->factory = $factory;
        $this->requestStack = $requestStack;
    }

    public function createMainMenu(): ItemInterface
    {
        $current = '';
        if ($this->requestStack->getCurrentRequest()) {
            $current = $this->requestStack->getCurrentRequest()->get('_route');
        }
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav ms-auto mb-2 mb-lg-0');
        $menu->addChild(
            'homepage',
            [
                'route' => 'app_web_homepage',
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link'.('app_web_homepage' === $current ? ' active' : ''),
                ],
            ]
        );
        $menu->addChild(
            'projects',
            [
                'route' => 'app_web_projects_list',
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link'.('app_web_projects_list' === $current || 'app_web_project_detail' === $current || 'app_web_project_placeholder' === $current ? ' active' : ''),
                ],
            ]
        );
        $menu->addChild(
            'contact',
            [
                'route' => 'app_web_contact',
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link'.('app_web_contact' === $current ? ' active' : ''),
                ],
            ]
        );

        return $menu;
    }

    public function createLanguagesMenu(): ItemInterface
    {
        $locale = '';
        if ($this->requestStack->getCurrentRequest()) {
            $locale = $this->requestStack->getCurrentRequest()->get('_locale');
        }
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav col-md-4 justify-content-center');
        foreach (LanguageEnum::getChoices() as $key => $language) {
            if ($locale !== $language) {
                $menu->addChild(
                    $key,
                    [
                        'route' => 'app_web_change_to_language',
                        'routeParameters' => [
                            'language' => $language,
                        ],
                        'attributes' => [
                            'class' => 'nav-item',
                        ],
                        'linkAttributes' => [
                            'class' => 'nav-link px-2 text-secondary',
                        ],
                    ]
                );
            }
        }

        return $menu;
    }
}
