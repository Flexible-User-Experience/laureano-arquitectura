<?php

namespace App\Model;

use Money\Money;

class MonthlyPerformance
{
    private int $year;
    private int $newCustomersAmount;
    private Money $invoiced;
    private Money $expensed;
    private Money $result;

    public function __construct(int $year, int $newCustomersAmount, Money $invoiced, Money $expensed)
    {
        $this->year = $year;
        $this->newCustomersAmount = $newCustomersAmount;
        $this->invoiced = $invoiced;
        $this->expensed = $expensed;
        $this->result = $invoiced->subtract($expensed);
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

    public function getNewCustomersAmount(): int
    {
        return $this->newCustomersAmount;
    }

    public function setNewCustomersAmount(int $newCustomersAmount): self
    {
        $this->newCustomersAmount = $newCustomersAmount;

        return $this;
    }

    public function getInvoiced(): Money
    {
        return $this->invoiced;
    }

    public function setInvoiced(Money $invoiced): self
    {
        $this->invoiced = $invoiced;

        return $this;
    }

    public function getExpensed(): Money
    {
        return $this->expensed;
    }

    public function setExpensed(Money $expensed): self
    {
        $this->expensed = $expensed;

        return $this;
    }

    public function getResult(): Money
    {
        return $this->result;
    }

    public function setResult(Money $result): self
    {
        $this->result = $result;

        return $this;
    }
}
