<?php

namespace App\Twig\Components;

use App\Repository\ProjectRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dashboard_performance_chart')]
class DashboardPerformanceChart
{
    private ProjectRepository $pr;

    public function __construct(ProjectRepository $pr)
    {
        $this->pr = $pr;
    }

    public function getProjects(): int
    {
        return count($this->pr->getActiveAndShowInFrontendSortedByPosition());
    }
}
