<?php

namespace App\Twig\Components;

use App\Entity\AbstractBase;
use App\Enum\ReceiptYearMonthEnum;
use App\Model\MonthlyPerformance;
use App\Repository\CustomerRepository;
use App\Repository\ExpenseRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Money\Currency;
use Money\Money;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('all_years_performance_table')]
class AllYearsPerformanceTableComponent
{
    public const FIRST_YEAR = ReceiptYearMonthEnum::APP_FIRST_YEAR;
    private InvoiceRepository $invoiceRepository;
    private ExpenseRepository $expenseRepository;
    private CustomerRepository $customerRepository;
    private Money $totalInvoiced;
    private Money $totalExpensed;
    private Money $totalResult;
    private int $totalNewCustomers = 0;

    public function __construct(InvoiceRepository $invoiceRepository, ExpenseRepository $expenseRepository, CustomerRepository $customerRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->expenseRepository = $expenseRepository;
        $this->customerRepository = $customerRepository;
        $this->totalInvoiced = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        $this->totalExpensed = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        $this->totalResult = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getMonthlyPerformances(): array
    {
        $result = [];
        $today = new \DateTimeImmutable();
        for ($year = self::FIRST_YEAR; $year <= (int) $today->format('Y'); ++$year) {
            $invoiced = new Money($this->invoiceRepository->getYearlyIncomingAmountForYear($year), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
            $expensed = new Money($this->expenseRepository->getYearlyExpensedAmountForYear($year), new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
            $totalNewCustomers = $this->customerRepository->getCumulativeNewCustomersAmountUpToYear($year) - $this->totalNewCustomers;
            $monthlyPerformance = new MonthlyPerformance($year, $totalNewCustomers, $invoiced, $expensed);
            $result[] = $monthlyPerformance;
            $this->totalInvoiced = $this->totalInvoiced->add($invoiced);
            $this->totalExpensed = $this->totalExpensed->add($expensed);
            $this->totalResult = $this->totalResult->add($monthlyPerformance->getResult());
            $this->totalNewCustomers += $totalNewCustomers;
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

    public function getTotalNewCustomers(): int
    {
        return $this->totalNewCustomers;
    }
}
