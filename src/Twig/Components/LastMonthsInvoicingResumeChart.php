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

#[AsTwigComponent('last_months_invoicing_resume_chart')]
class LastMonthsInvoicingResumeChart
{
    public const RED = 'rgb(255, 99, 132)';
    public const YELLOW = 'rgb(255, 205, 86)';
    public const GREEN = 'rgb(75, 192, 192)';
    public const BLUE = 'rgb(54, 162, 235)';
    public const BLACK = 'rgb(35, 35, 35)';
    public const LIGHT_GREY = 'rgb(235, 235, 235)';
    public const WHITE = 'rgb(255, 255, 255)';

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
        $sales = [];
        $expenses = [];
        $results = [];
        $zeros = [];
        $date = new \DateTime();
        $date->sub(new \DateInterval('P24M'));
        $interval = new \DateInterval('P1M');
        for ($i = 0; $i <= 24; ++$i) {
            $sale = $this->mf->asFloat(new Money($this->ir->getMonthlyIncomingAmountForDate($date), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)));
            $expense = $this->mf->asFloat(new Money($this->er->getMonthlyIncomingAmountForDate($date), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)));
            $result = $sale - $expense;
            $labels[] = ReceiptYearMonthEnum::getShortTranslatedMonthEnumArray()[(int) $date->format('n')].'. '.$date->format('Y');
            $sales[] = round($sale);
            $expenses[] = round($expense);
            $results[] = round($result);
            $zeros[] = 0.0;
            $date->add($interval);
        }
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->ts->trans('Sales'),
                    'data' => $sales,
                    'borderColor' => self::GREEN,
                    'backgroundColor' => self::GREEN,
                    'order' => 4,
                    'tension' => 0.25,
                    'fill' => false,
                    'animation' => true,
                ],
                [
                    'label' => $this->ts->trans('Expenses'),
                    'data' => $expenses,
                    'borderColor' => self::RED,
                    'backgroundColor' => self::RED,
                    'order' => 3,
                    'tension' => 0.25,
                    'fill' => false,
                    'animation' => true,
                ],
                [
                    'label' => $this->ts->trans('Results'),
                    'data' => $results,
                    'borderColor' => self::BLUE,
                    'backgroundColor' => self::BLUE,
                    'order' => 2,
                    'tension' => 0.25,
                    'fill' => false,
                    'animation' => true,
                ],
                [
                    'label' => $this->ts->trans('Zeros'),
                    'data' => $zeros,
                    'borderColor' => self::BLACK,
                    'backgroundColor' => self::BLACK,
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
        ]);

        return $chart;
    }
}
