<?php

namespace App\Menu;

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
                    'class' => 'nav-link'.('app_web_projects_list' === $current ? ' active' : ''),
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
}
