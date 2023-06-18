<?php

namespace App\Twig\Components;

use App\Repository\InvoiceRepository;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('unpaid_invoices_table')]
class UnpaidInvoicesTable
{
    private InvoiceRepository $ir;

    public function __construct(InvoiceRepository $ir)
    {
        $this->ir = $ir;
    }

    public function getUnpaidInvoices(): array
    {
        return $this->ir->getUnpaidInvoicesSortedByDate();
    }
}
