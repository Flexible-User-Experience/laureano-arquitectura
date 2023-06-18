<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

trait TotalInvoicedTrait
{
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $totalInvoicedAmount = 0;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['default' => AbstractBase::DEFAULT_CURRENCY_STRING])]
    private string $totalInvoicedCurrency = AbstractBase::DEFAULT_CURRENCY_STRING;

    public function getTotalInvoicedAmount(): int
    {
        return $this->totalInvoicedAmount;
    }

    public function setTotalInvoicedAmount(int $totalInvoicedAmount): self
    {
        $this->totalInvoicedAmount = $totalInvoicedAmount;

        return $this;
    }

    public function getTotalInvoicedCurrency(): string
    {
        return $this->totalInvoicedCurrency;
    }

    public function setTotalInvoicedCurrency(string $totalInvoicedCurrency): self
    {
        $this->totalInvoicedCurrency = $totalInvoicedCurrency;

        return $this;
    }

    public function getTotalInvoiced(): Money
    {
        $result = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        if ($this->getTotalInvoicedAmount() && $this->getTotalInvoicedCurrency()) {
            $result = new Money($this->getTotalInvoicedAmount(), new Currency($this->getTotalInvoicedCurrency()));
        }

        return $result;
    }

    public function setTotalInvoiced(Money $total): self
    {
        $this
            ->setTotalInvoicedAmount((int) $total->getAmount())
            ->setTotalInvoicedCurrency($total->getCurrency()->getCode())
        ;

        return $this;
    }
}
