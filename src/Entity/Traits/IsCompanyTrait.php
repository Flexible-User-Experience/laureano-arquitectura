<?php

namespace App\Entity\Traits;

trait IsCompanyTrait
{
    public function isCompany(): bool
    {
        return $this->isCompany;
    }

    public function getIsCompany(): bool
    {
        return $this->isCompany();
    }

    public function isFreelancer(): bool
    {
        return !$this->isCompany();
    }

    public function getIsFreelancer(): bool
    {
        return $this->isFreelancer();
    }

    public function setIsCompany(bool $isCompany): self
    {
        $this->isCompany = $isCompany;

        return $this;
    }
}
