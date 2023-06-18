<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

trait UnitPriceTrait
{
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $unitPriceAmount = 0;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['default' => AbstractBase::DEFAULT_CURRENCY_STRING])]
    private string $unitPriceCurrency = AbstractBase::DEFAULT_CURRENCY_STRING;

    public function getUnits(): float
    {
        return $this->units;
    }

    public function getUnitsString(): string
    {
        return self::convertFloatAsString($this->getUnits());
    }

    public function setUnits(float $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function getUnitPriceAmount(): int
    {
        return $this->unitPriceAmount;
    }

    public function setUnitPriceAmount(int $unitPriceAmount): self
    {
        $this->unitPriceAmount = $unitPriceAmount;

        return $this;
    }

    public function getUnitPriceCurrency(): string
    {
        return $this->unitPriceCurrency;
    }

    public function setUnitPriceCurrency(string $unitPriceCurrency): self
    {
        $this->unitPriceCurrency = $unitPriceCurrency;

        return $this;
    }

    public function getUnitPrice(): Money
    {
        $result = new Money(0, new Currency(AbstractBase::DEFAULT_CURRENCY_STRING));
        if ($this->getUnitPriceAmount() && $this->getUnitPriceCurrency()) {
            $result = new Money($this->getUnitPriceAmount(), new Currency($this->getUnitPriceCurrency()));
        }

        return $result;
    }

    public function setUnitPrice(Money $unitPrice): self
    {
        $this
            ->setUnitPriceAmount((int) $unitPrice->getAmount())
            ->setUnitPriceCurrency($unitPrice->getCurrency()->getCode())
        ;

        return $this;
    }
}
