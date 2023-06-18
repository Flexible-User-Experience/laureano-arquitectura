<?php

namespace App\Model;

use Money\Money;

class TopTenCustomerEarnings
{
    private int $number;
    private string $customer;
    private string $city;
    private Money $earnings;

    public function __construct(int $number, string $customer, string $city, Money $earnings)
    {
        $this->number = $number;
        $this->customer = $customer;
        $this->city = $city;
        $this->earnings = $earnings;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCustomer(): string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getEarnings(): Money
    {
        return $this->earnings;
    }

    public function setEarnings(Money $earnings): self
    {
        $this->earnings = $earnings;

        return $this;
    }
}
