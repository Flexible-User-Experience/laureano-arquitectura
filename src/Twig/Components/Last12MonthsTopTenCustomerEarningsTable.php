<?php

namespace App\Twig\Components;

use App\Entity\AbstractBase;
use App\Model\TopTenCustomerEarnings;
use App\Repository\CustomerRepository;
use Money\Currency;
use Money\Money;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('last_12_months_top_ten_customer_earnings_table')]
class Last12MonthsTopTenCustomerEarningsTable
{
    protected CustomerRepository $customerRepository;
    protected Money $total;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->total = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
    }

    protected function getEarningsArray(): array
    {
        return $this->customerRepository->getLast12MonthsTopTenCustomerEarnings();
    }

    public function getTopTenCustomerEarnings(): array
    {
        $result = [];
        $number = 1;
        foreach ($this->getEarningsArray() as $item) {
            $earnings = new Money($item['invoiced'], new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
            $topTenCustomerEarnings = new TopTenCustomerEarnings(
                $number,
                $item['fiscalName'],
                $item['city'],
                $earnings,
            );
            $result[] = $topTenCustomerEarnings;
            $this->total = $this->total->add($earnings);
            ++$number;
        }

        return $result;
    }

    public function getTotal(): Money
    {
        return $this->total;
    }
}
