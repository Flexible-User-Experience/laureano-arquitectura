<?php

namespace App\Twig\Components;

use App\Entity\AbstractBase;
use App\Model\YearlyMedians;
use App\Repository\ExpenseRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Money\Currency;
use Money\Money;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('all_years_medians_table')]
class AllYearsMediansTableComponent
{
    private InvoiceRepository $invoiceRepository;
    private ExpenseRepository $expenseRepository;
    private Money $totalInvoiced;
    private Money $totalExpensed;
    private Money $totalResult;
    private float $totalPerformance = 0;

    public function __construct(InvoiceRepository $invoiceRepository, ExpenseRepository $expenseRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->expenseRepository = $expenseRepository;
        $this->totalInvoiced = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        $this->totalExpensed = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        $this->totalResult = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getYearlyMedians(): array
    {
        $result = [];
        $today = new \DateTimeImmutable();
        for ($year = AllYearsPerformanceTableComponent::FIRST_YEAR; $year <= (int) $today->format('Y'); ++$year) {
            $invoiced = (new Money($this->invoiceRepository->getYearlyIncomingAmountForYear($year), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)))->divide(12);
            $expensed = (new Money($this->expenseRepository->getYearlyExpensedAmountForYear($year), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING)))->divide(12);
            $yearlyMedians = new YearlyMedians($year, $invoiced, $expensed);
            $result[] = $yearlyMedians;
            $this->totalInvoiced = $this->totalInvoiced->add($invoiced);
            $this->totalExpensed = $this->totalExpensed->add($expensed);
            $this->totalResult = $this->totalResult->add($yearlyMedians->getMonthlyMedianResult());
        }
        if (!$this->totalInvoiced->isZero()) {
            $this->totalPerformance = (float) $this->totalResult->divide($this->totalInvoiced->getAmount() / 100, Money::ROUND_DOWN)->getAmount();
        }

        return $result;
    }

    public function getTotalInvoiced(): Money
    {
        return $this->totalInvoiced;
    }

    public function getTotalExpensed(): Money
    {
        return $this->totalExpensed;
    }

    public function getTotalResult(): Money
    {
        return $this->totalResult;
    }

    public function getTotalPerformance(): float
    {
        return $this->totalPerformance;
    }
}
