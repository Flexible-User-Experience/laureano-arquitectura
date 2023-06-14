<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait PositionTrait
{
    #[ORM\Column(type: Types::SMALLINT, nullable: false, options: ['default' => 1])]
    private int $position = 1;

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
