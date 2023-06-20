<?php

namespace App\Menu;

use App\Entity\User;
use App\Manager\GoogleAnalyticsManager;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminTopRightNavMenuBuilder
{
    private FactoryInterface $mf;
    private TranslatorInterface $ts;
    private Security $ss;
    private GoogleAnalyticsManager $gam;

    public function __construct(FactoryInterface $mf, TranslatorInterface $ts, Security $ss, GoogleAnalyticsManager $gam)
    {
        $this->mf = $mf;
        $this->ts = $ts;
        $this->ss = $ss;
        $this->gam = $gam;
    }

    public function createRightTopNavMenu(): ItemInterface
    {
        $username = '';
        $user = $this->ss->getUser();
        $userHasGoogleAccessToken = false;
        if ($user instanceof User) {
            $username = $user->getUsername();
            $userHasGoogleAccessToken = !is_null($user->getGoogleAccessToken());
        }
        $menu = $this->mf->createItem('topnav');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        if ((!$userHasGoogleAccessToken && !$user->getGoogleCredentialsAccepted()) || ($user->getGoogleCredentialsAccepted() && array_key_exists('error', $user->getGoogleAccessToken()))) {
            $menu
                ->addChild(
                    'usergoogleaccesstoken',
                    [
                        'label' => '<span class="btn btn-xs btn-warning" style="line-height:1.3"><i class="fas fa-exclamation-triangle fa-fw"></i> '.$this->ts->trans('Connect Google Api Warning').'</span>',
                        'uri' => $this->gam->getGoogleApiClient()->createAuthUrl(),
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
