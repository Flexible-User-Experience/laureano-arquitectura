<?php

namespace App\Twig\Components;

use App\Repository\ContactMessageRepository;
use App\Repository\ProjectRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dashboard_performance_chart')]
class DashboardPerformanceChart
{
    private ProjectRepository $pr;
    private ContactMessageRepository $cmr;

    public function __construct(ProjectRepository $pr, ContactMessageRepository $cmr)
    {
        $this->pr = $pr;
        $this->cmr = $cmr;
    }

    public function getTotalProjectsAmount(): int
    {
        return count($this->pr->findAll());
    }

    public function getTotalContactMessagesAmount(): int
    {
        return count($this->cmr->findAll());
    }
}
