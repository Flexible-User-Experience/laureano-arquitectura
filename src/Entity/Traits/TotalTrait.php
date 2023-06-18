<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

trait TotalTrait
{
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $totalAmount = 0;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['default' => AbstractBase::DEFAULT_CURRENCY_STRING])]
    private string $totalCurrency = AbstractBase::DEFAULT_CURRENCY_STRING;

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getTotalCurrency(): string
    {
        return $this->totalCurrency;
    }

    public function setTotalCurrency(string $totalCurrency): self
    {
        $this->totalCurrency = $totalCurrency;

        return $this;
    }

    public function getTotal(): Money
    {
        $result = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        if ($this->getTotalAmount() && $this->getTotalCurrency()) {
            $result = new Money($this->getTotalAmount(), new Currency($this->getTotalCurrency()));
        }

        return $result;
    }

    public function setTotal(Money $total): self
    {
        $this
            ->setTotalAmount((int) $total->getAmount())
            ->setTotalCurrency($total->getCurrency()->getCode())
        ;

        return $this;
    }
}
