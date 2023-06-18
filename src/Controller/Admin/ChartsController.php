<?php

namespace App\Controller\Admin;

use App\Block\AllYearsMediansBlock;
use App\Block\AllYearsPerformanceBlock;
use App\Block\Last12MonthsTopTenCustomerEarningsBlock;
use App\Block\LastMonthsInvoicingResumeBlock;
use App\Block\TopTenCustomerEarningsBlock;
use App\Block\TotalRetainingEarningsBlock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/charts')]
final class ChartsController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_charts')]
    public function mediaInlineSpendingAction(): Response
    {
        return $this->render('@App/admin/charts_dashboard.htm.twig', [
            'base_template' => '@App/Admin/layout.html.twig',
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
}
