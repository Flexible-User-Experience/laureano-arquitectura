<?php

namespace App\Entity\Traits;

use App\Entity\AbstractBase;
use DateTimeInterface;

trait BeginDateTrait
{
    public function getBeginDate(): ?DateTimeInterface
    {
        return $this->beginDate;
    }

    public function getBeginDateString(): string
    {
        return AbstractBase::convertDateAsString($this->getBeginDate());
    }

    public function setBeginDate(?DateTimeInterface $beginDate): self
    {
        $this->beginDate = $beginDate;

        return $this;
    }
}
