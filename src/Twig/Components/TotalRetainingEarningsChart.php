<?php

namespace App\Twig\Components;

use App\Entity\AbstractBase;
use App\Enum\ReceiptYearMonthEnum;
use App\Repository\ExpenseRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Money\Currency;
use Money\Money;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

#[AsTwigComponent('total_retaining_earnings_chart')]
class TotalRetainingEarningsChart
{
    private TranslatorInterface $ts;
    private ChartBuilderInterface $cb;
    private MoneyFormatter $mf;
    private InvoiceRepository $ir;
    private ExpenseRepository $er;

    public function __construct(TranslatorInterface $ts, ChartBuilderInterface $cb, MoneyFormatter $mf, InvoiceRepository $ir, ExpenseRepository $er)
    {
        $this->ts = $ts;
        $this->cb = $cb;
        $this->mf = $mf;
        $this->ir = $ir;
        $this->er = $er;
    }

    /**
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function getChart(): Chart
    {
        $chart = $this->cb->createChart(Chart::TYPE_LINE);
        $labels = [];
        $results = [];
        $currentResult = 0;
        $zeros = [];
        $date = new \DateTime();
        $iterations = $this->getTotalMountsSinceFirstYear($date);
        $date->sub(new \DateInterval('P'.$iterations.'M'));
        $interval = new \DateInterval('P1M');
        for ($i = 0; $i <= $iterations; ++$i) {
            $sale = $this->mf->asFloat(new Money($this->ir->getMonthlyIncomingAmountForDate($date), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)));
            $expense = $this->mf->asFloat(new Money($this->er->getMonthlyIncomingAmountForDate($date), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)));
            $result = $sale - $expense;
            $currentResult += $result;
            $labels[] = ReceiptYearMonthEnum::getShortTranslatedMonthEnumArray()[(int) $date->format('n')].'. '.$date->format('Y');
            $results[] = round($currentResult);
            $zeros[] = 0.0;
            $date->add($interval);
        }
        $chart
            ->setData([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $this->ts->trans('Results'),
                        'data' => $results,
                        'borderColor' => LastMonthsInvoicingResumeChart::RED,
                        'backgroundColor' => LastMonthsInvoicingResumeChart::RED,
                        'order' => 2,
                        'tension' => 0,
                        'fill' => false,
                        'animation' => true,
                    ],
                    [
                        'label' => $this->ts->trans('Zeros'),
                        'data' => $zeros,
                        'borderColor' => LastMonthsInvoicingResumeChart::BLACK,
                        'backgroundColor' => LastMonthsInvoicingResumeChart::BLACK,
                        'pointBorderWidth' => 0,
                        'pointStyle' => 'dash',
                        'borderWidth' => 2,
                        'borderDash' => [5, 5],
                        'order' => 1,
                        'tension' => 0,
                        'fill' => false,
                        'animation' => false,
                    ],
                ],
            ])
            ->setOptions([
                'animation' => false,
                'aspectRatio' => 2.5,
                'scales' => [
                    'yAxes' => [
                        'ticks' => [
                            'display' => true,
                        ],
                    ],
                ],
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ])
        ;

        return $chart;
    }

    /**
     * @throws \Exception
     */
    private function getTotalMountsSinceFirstYear(\DateTimeInterface $moment): int
    {
        $start = new \DateTime(AllYearsPerformanceTableComponent::FIRST_YEAR.'-01-01 00:00:00');
        $diff = $start->diff($moment);
        $yearsInMonths = ((int) $diff->format('%r%y')) * 12;
        $months = (int) $diff->format('%r%m');

        return $yearsInMonths + $months;
    }
}
