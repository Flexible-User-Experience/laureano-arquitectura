<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use DateTimeInterface;

trait EndDateTrait
{
    public function getEndDate(): ?DateTimeInterface
    {
        return $this->endDate;
    }

    public function getEndDateString(): string
    {
        return AbstractBase::convertDateAsString($this->getEndDate());
    }

    public function setEndDate(?DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
