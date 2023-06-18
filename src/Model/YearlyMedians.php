<?php

namespace App\Model;

use Money\Money;

class YearlyMedians
{
    private int $year;
    private Money $monthlyMedianInvoiced;
    private Money $monthlyMedianExpensed;
    private Money $monthlyMedianResult;
    private float $performance = 0;

    public function __construct(int $year, Money $monthlyMedianInvoiced, Money $monthlyMedianExpensed)
    {
        $this->year = $year;
        $this->monthlyMedianInvoiced = $monthlyMedianInvoiced;
        $this->monthlyMedianExpensed = $monthlyMedianExpensed;
        $subtract = $monthlyMedianInvoiced->subtract($monthlyMedianExpensed);
        $this->monthlyMedianResult = $subtract;
        if (!$monthlyMedianInvoiced->isZero()) {
            $this->performance = (float) $subtract->divide($monthlyMedianInvoiced->getAmount() / 100, Money::ROUND_DOWN)->getAmount();
        }
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonthlyMedianInvoiced(): Money
    {
        return $this->monthlyMedianInvoiced;
    }

    public function setMonthlyMedianInvoiced(Money $monthlyMedianInvoiced): self
    {
        $this->monthlyMedianInvoiced = $monthlyMedianInvoiced;

        return $this;
    }

    public function getMonthlyMedianExpensed(): Money
    {
        return $this->monthlyMedianExpensed;
    }

    public function setMonthlyMedianExpensed(Money $monthlyMedianExpensed): self
    {
        $this->monthlyMedianExpensed = $monthlyMedianExpensed;

        return $this;
    }

    public function getMonthlyMedianResult(): Money
    {
        return $this->monthlyMedianResult;
    }

    public function setMonthlyMedianResult(Money $monthlyMedianResult): self
    {
        $this->monthlyMedianResult = $monthlyMedianResult;

        return $this;
    }

    public function getPerformance(): float
    {
        return $this->performance;
    }

    public function setPerformance(float $performance): self
    {
        $this->performance = $performance;

        return $this;
    }
}
