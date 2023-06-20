<?php

namespace App\Twig\Components;

use App\Manager\GoogleAnalyticsManager;
use App\Repository\ContactMessageRepository;
use App\Repository\CustomerRepository;
use App\Repository\ExpenseRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProviderRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dashboard_performance_chart')]
class DashboardPerformanceChart
{
    private ProjectRepository $pr;
    private ContactMessageRepository $cmr;
    private CustomerRepository $cr;
    private ProviderRepository $pvr;
    private ExpenseRepository $er;
    private InvoiceRepository $ir;
    private GoogleAnalyticsManager $gam;

    public function __construct(ProjectRepository $pr, ContactMessageRepository $cmr, CustomerRepository $cr, ProviderRepository $pvr, ExpenseRepository $er, InvoiceRepository $ir, GoogleAnalyticsManager $gam)
    {
        $this->pr = $pr;
        $this->cmr = $cmr;
        $this->cr = $cr;
        $this->pvr = $pvr;
        $this->er = $er;
        $this->ir = $ir;
        $this->gam = $gam;
    }

    public function getTotalProjectsAmount(): int
    {
        return $this->pr->getTotalProjectsAmount();
    }

    public function getTotalContactMessagesAmount(): int
    {
        return $this->cmr->getContactMessagesAmount();
    }

    public function getResponsePendingContactMessagesAmount(): int
    {
        return $this->cmr->getResponsePendingContactMessagesAmount();
    }

    public function getTotalCustomersAmount(): int
    {
        return $this->cr->getTotalCustomersAmount();
    }

    public function getTotalProvidersAmount(): int
    {
        return $this->pvr->getTotalProvidersAmount();
    }

    public function getTotalExpensesAmount(): int
    {
        return $this->er->getTotalExpensesAmount();
    }

    public function getTotalInvoicesAmount(): int
    {
        return $this->ir->getTotalInvoicesAmount();
    }

    public function get365DaysAgoTotalVisitsAmount(): int
    {
        return $this->gam->get365DaysAgoTotalVisitsAmount();
    }

    public function get30DaysAgoTotalVisitsAmount(): int
    {
        return $this->gam->get30DaysAgoTotalVisitsAmount();
    }
}
