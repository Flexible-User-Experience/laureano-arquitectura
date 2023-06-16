<?php

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AdminTopRightNavMenuBuilder
{
    private FactoryInterface $mf;
    private Security $ss;

    public function __construct(FactoryInterface $mf, Security $ss)
    {
        $this->mf = $mf;
        $this->ss = $ss;
    }

    public function createRightTopNavMenu(): ItemInterface
    {
        $username = '';
        $user = $this->ss->getUser();
        if ($user instanceof User) {
            $username = $user->getUsername();
        }
        $menu = $this->mf->createItem('topnav');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        $menu
            ->addChild(
                'homepage',
                [
                    'label' => '<i class="fas fa-link"></i>',
                    'route' => 'app_web_homepage',
                ]
            )
            ->setExtras(
                [
                    'safe_label' => true,
                ]
            )
        ;
        if ($username) {
            $menu
                ->addChild(
                    'username',
                    [
                        'label' => '<i class="far fa-user" style="margin-right:5px"></i> '.$username,
                        'uri' => '#',
                    ]
                )
                ->setExtras(
                    [
                        'safe_label' => true,
                    ]
                )
            ;
        }
        $menu
            ->addChild(
                'logout',
                [
                    'label' => '<i class="fa fa-power-off text-white"></i>',
                    'route' => 'sonata_user_admin_security_logout',
                ]
            )
            ->setExtras(
                [
                    'safe_label' => true,
                ]
            )
        ;

        return $menu;
    }
}
