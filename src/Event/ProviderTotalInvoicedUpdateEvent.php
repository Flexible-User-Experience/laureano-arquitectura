<?php

namespace App\Event;

use App\Entity\Provider;
use Symfony\Contracts\EventDispatcher\Event;

class ProviderTotalInvoicedUpdateEvent extends Event
{
    public const UPDATE = 'provider.total_invoiced_update';

    private Provider $provider;
    private int $totalInvoicedDelta;

    public function __construct(Provider $provider, int $totalInvoicedDelta)
    {
        $this->provider = $provider;
        $this->totalInvoicedDelta = $totalInvoicedDelta;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getTotalInvoicedDelta(): int
    {
        return $this->totalInvoicedDelta;
    }
}
