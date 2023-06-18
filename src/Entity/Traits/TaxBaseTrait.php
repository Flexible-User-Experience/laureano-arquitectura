<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

trait TaxBaseTrait
{
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $taxBaseAmount = 0;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['default' => AbstractBase::DEFAULT_CURRENCY_STRING])]
    private string $taxBaseCurrency = AbstractBase::DEFAULT_CURRENCY_STRING;

    public function getTaxBaseAmount(): int
    {
        return $this->taxBaseAmount;
    }

    public function setTaxBaseAmount(int $taxBaseAmount): self
    {
        $this->taxBaseAmount = $taxBaseAmount;

        return $this;
    }

    public function getTaxBaseCurrency(): string
    {
        return $this->taxBaseCurrency;
    }

    public function setTaxBaseCurrency(string $taxBaseCurrency): self
    {
        $this->taxBaseCurrency = $taxBaseCurrency;

        return $this;
    }

    public function getTaxBase(): Money
    {
        $result = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        if ($this->getTaxBaseAmount() && $this->getTaxBaseCurrency()) {
            $result = new Money($this->getTaxBaseAmount(), new Currency($this->getTaxBaseCurrency()));
        }

        return $result;
    }

    public function setTaxBase(Money $taxBase): self
    {
        $this
            ->setTaxBaseAmount((int) $taxBase->getAmount())
            ->setTaxBaseCurrency($taxBase->getCurrency()->getCode())
        ;

        return $this;
    }
}
