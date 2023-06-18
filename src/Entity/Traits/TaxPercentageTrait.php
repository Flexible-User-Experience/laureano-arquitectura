<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait TaxPercentageTrait
{
    #[ORM\Column(type: Types::FLOAT, precision: 2, options: ['default' => self::DEFAULT_VALUE_ADDED_TAX])]
    private float $taxPercentage = self::DEFAULT_VALUE_ADDED_TAX;

    public function getTaxPercentage(): float
    {
        return $this->taxPercentage;
    }

    public function setTaxPercentage(float $taxPercentage): self
    {
        $this->taxPercentage = $taxPercentage;

        return $this;
    }
}
