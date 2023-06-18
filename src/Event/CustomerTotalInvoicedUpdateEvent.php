<?php

namespace App\Event;

use App\Entity\Customer;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerTotalInvoicedUpdateEvent extends Event
{
    public const UPDATE = 'customer.total_invoiced_update';

    private Customer $customer;
    private int $totalInvoicedDelta;

    public function __construct(Customer $customer, int $totalInvoicedDelta)
    {
        $this->customer = $customer;
        $this->totalInvoicedDelta = $totalInvoicedDelta;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getTotalInvoicedDelta(): int
    {
        return $this->totalInvoicedDelta;
    }
}
