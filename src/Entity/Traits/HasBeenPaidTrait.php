<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait HasBeenPaidTrait
{
    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasBeenPaid = false;

    public function hasBeenPaid(): bool
    {
        return $this->hasBeenPaid;
    }

    public function getHasBeenPaid(): bool
    {
        return $this->hasBeenPaid();
    }

    public function setHasBeenPaid(bool $hasBeenPaid): self
    {
        $this->hasBeenPaid = $hasBeenPaid;

        return $this;
    }
}
