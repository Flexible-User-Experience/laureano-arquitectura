<?php

namespace App\Twig\Components;

use App\Repository\WorkingDayRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('dashboard_performance_chart')]
class DashboardPerformanceChart
{
    private TranslatorInterface $ts;
    private ChartBuilderInterface $cb;
    private WorkingDayRepository $wdr;

    public function __construct(TranslatorInterface $ts, ChartBuilderInterface $cb, WorkingDayRepository $wdr)
    {
        $this->ts = $ts;
        $this->cb = $cb;
        $this->wdr = $wdr;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getChart(): Chart
    {
        $chart = $this->cb->createChart(Chart::TYPE_DOUGHNUT);
        $last30WorkingDaysPerformance = (int) round($this->wdr->lastWorkingDaysPerformance(30));
        $last180WorkingDaysPerformance = (int) round($this->wdr->lastWorkingDaysPerformance(180));
        $last365WorkingDaysPerformance = (int) round($this->wdr->lastWorkingDaysPerformance(365));
        $chart
            ->setData([
                'datasets' => [
                    [
                        'label' => $this->ts->trans('Last 10 Days'),
                        'data' => [$last30WorkingDaysPerformance, 100 - $last30WorkingDaysPerformance],
                        'backgroundColor' => [
                            LastMonthsInvoicingResumeChart::RED,
                            LastMonthsInvoicingResumeChart::LIGHT_GREY,
                        ],
                    ],
                    [
                        'label' => $this->ts->trans('Last Month'),
                        'data' => [$last180WorkingDaysPerformance, 100 - $last180WorkingDaysPerformance],
                        'backgroundColor' => [
                            LastMonthsInvoicingResumeChart::GREEN,
                            LastMonthsInvoicingResumeChart::LIGHT_GREY,
                        ],
                    ],
                    [
                        'label' => $this->ts->trans('Last Year'),
                        'data' => [$last365WorkingDaysPerformance, 100 - $last365WorkingDaysPerformance],
                        'backgroundColor' => [
                            LastMonthsInvoicingResumeChart::BLUE,
                            LastMonthsInvoicingResumeChart::LIGHT_GREY,
                        ],
                    ],
                ],
            ])
            ->setOptions([
                'animation' => false,
                'circumference' => 360,
                'rotation' => 0,
                'radius' => '100%',
                'cutout' => '25%',
                'events' => [],
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'tooltip' => [
                        'enabled' => false,
                    ],
                    'datalabels' => [
                        'color' => [
                            LastMonthsInvoicingResumeChart::WHITE,
                            LastMonthsInvoicingResumeChart::LIGHT_GREY,
                        ],
                        'font' => [
                            'size' => 18,
                            'weight' => 'bold',
                        ],
                    ],
                ],
            ])
        ;

        return $chart;
    }
}
