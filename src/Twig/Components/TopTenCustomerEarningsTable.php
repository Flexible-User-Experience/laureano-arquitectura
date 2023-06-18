<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('top_ten_customer_earnings_table')]
class TopTenCustomerEarningsTable extends Last12MonthsTopTenCustomerEarningsTable
{
    protected function getEarningsArray(): array
    {
        return $this->customerRepository->getTopTenCustomerEarnings();
    }
}
