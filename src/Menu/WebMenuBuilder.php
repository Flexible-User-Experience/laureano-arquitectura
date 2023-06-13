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

    public function createMainMenu(array $options): ItemInterface
    {
        $current = '';
        if ($this->requestStack->getCurrentRequest()) {
            $current = $this->requestStack->getCurrentRequest()->get('_route');
        }
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navbar-nav ms-auto mb-2 mb-lg-0');
        // $menu->setChildrenAttribute('class', 'navbar-nav mx-5 h-100 menu');
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
                    'class' => 'nav-link'.('app_web_projects_list' === $current || 'app_web_project_detail' === $current ? ' active' : ''),
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

    public function createLanguagesMenu(array $options): ItemInterface
    {
        $current = '';
        if ($this->requestStack->getCurrentRequest()) {
            $current = $this->requestStack->getCurrentRequest()->get('_route');
        }
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav col-md-4 justify-content-center');
        $menu->addChild(
            'catalan',
            [
                'route' => 'app_web_change_to_language',
                'routeParameters' => [
                    'language' => LanguageEnum::CATALAN->value,
                ],
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link px-2 text-secondary',
                ],
            ]
        );
        $menu->addChild(
            'spanish',
            [
                'route' => 'app_web_change_to_language',
                'routeParameters' => [
                    'language' => LanguageEnum::SPANISH->value,
                ],
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link px-2 text-secondary',
                ],
            ]
        );
        $menu->addChild(
            'english',
            [
                'route' => 'app_web_change_to_language',
                'routeParameters' => [
                    'language' => LanguageEnum::ENGLISH->value,
                ],
                'attributes' => [
                    'class' => 'nav-item',
                ],
                'linkAttributes' => [
                    'class' => 'nav-link px-2 text-secondary',
                ],
            ]
        );

        return $menu;
    }
}
