<?php

namespace App\Controller\Admin;

use App\Block\AllYearsMediansBlock;
use App\Block\AllYearsPerformanceBlock;
use App\Block\Last12MonthsTopTenCustomerEarningsBlock;
use App\Block\LastMonthsInvoicingResumeBlock;
use App\Block\TopTenCustomerEarningsBlock;
use App\Block\TotalRetainingEarningsBlock;
use App\Entity\User;
use App\Manager\GoogleAnalyticsManager;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/charts')]
final class ChartsController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_charts')]
    public function mediaInlineSpendingAction(): Response
    {
        return $this->render('@App/admin/charts_dashboard.htm.twig', [
            'base_template' => '@App/admin/layout.html.twig',
            'blocks' => [
                'top' => [
                    [
                        'type' => LastMonthsInvoicingResumeBlock::class,
                        'class' => 'col-xs-12',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                    [
                        'type' => TotalRetainingEarningsBlock::class,
                        'class' => 'col-xs-12',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                    [
                        'type' => AllYearsMediansBlock::class,
                        'class' => 'col-xs-6',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                    [
                        'type' => AllYearsPerformanceBlock::class,
                        'class' => 'col-xs-6',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                    [
                        'type' => Last12MonthsTopTenCustomerEarningsBlock::class,
                        'class' => 'col-xs-6',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                    [
                        'type' => TopTenCustomerEarningsBlock::class,
                        'class' => 'col-xs-6',
                        'roles' => ['ROLE_ADMIN'],
                        'settings' => [],
                    ],
                ],
                'left' => [],
                'center' => [],
                'right' => [],
                'bottom' => [],
            ],
        ]);
    }

    #[Route('/google-user-oauth-token', name: 'admin_google_user_oauth_token')]
    public function googleUserOauthToken(Request $request, GoogleAnalyticsManager $gam, TranslatorInterface $ts, UserRepository $ur): Response
    {
        $authCode = $request->get('code');
        if ($authCode) {
            $accessToken = $gam->getGoogleApiClient()->fetchAccessTokenWithAuthCode($authCode);
            // check to see if there was an error
            if (array_key_exists('error', $accessToken)) {
                throw $this->createNotFoundException(implode(', ', $accessToken));
            }
            $gam->getGoogleApiClient()->setAccessToken($accessToken);
            $gam->setGoogleAnalyticsServiceByGoogleApiClient($gam->getGoogleApiClient());
            /** @var User $user */
            $user = $request->getUser();
            if ($user instanceof User) {
                $user->setGoogleAccessToken($accessToken);
                $ur->update(true);
                $this->addFlash(
                    'sonata_flash_success',
                    $ts->trans('layout.connect_google_calendar_api_success')
                );
            } else {
                throw $this->createAccessDeniedException('User logged not found');
            }
        } else {
            throw $this->createAccessDeniedException('No auth code URL param found');
        }

        return $this->redirectToRoute('sonata_admin_dashboard');
    }
}
