<?php

namespace App\Entity;

use App\Entity\Traits\EmailTrait;
use App\Entity\Traits\IsCompanyTrait;
use App\Entity\Traits\LocaleTrait;
use App\Entity\Traits\MobileNumberTrait;
use App\Entity\Traits\WebsiteTrait;

abstract class AbstractAccount extends AbstractBase
{
    use EmailTrait;
    use IsCompanyTrait;
    use LocaleTrait;
    use MobileNumberTrait;
    use WebsiteTrait;

    protected string $fiscalIdentificationCode;
    protected string $fiscalName;
    protected ?string $commercialName = null;
    protected ?string $address1 = null;
    protected ?string $address2 = null;
    protected ?string $postalCode = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $country = null;
    protected ?string $email = null;
    protected ?string $phoneNumber = null;
    protected bool $isCompany;

    public function getCommercialOrFiscalName(): string
    {
        $result = AbstractBase::DEFAULT_NULL_STRING;
        if ($this->getId()) {
            if ($this->getCommercialName()) {
                $result = $this->getCommercialName();
            } else {
                $result = $this->getFiscalName();
            }
        }

        return $result;
    }

    public function getFiscalAndCommercialName(): string
    {
        return $this->id ? $this->getFiscalName().($this->getCommercialName() ? ' ('.$this->getCommercialName().')' : '') : AbstractBase::DEFAULT_NULL_STRING;
    }

    public function getFiscalIdentificationCode(): string
    {
        return $this->fiscalIdentificationCode;
    }

    public function setFiscalIdentificationCode(string $fiscalIdentificationCode): self
    {
        $this->fiscalIdentificationCode = $fiscalIdentificationCode;

        return $this;
    }

    public function getFiscalName(): string
    {
        return $this->fiscalName;
    }

    public function setFiscalName(string $fiscalName): self
    {
        $this->fiscalName = $fiscalName;

        return $this;
    }

    public function getCommercialName(): ?string
    {
        return $this->commercialName;
    }

    public function setCommercialName(?string $commercialName): self
    {
        $this->commercialName = $commercialName;

        return $this;
    }

    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getOneLineLocation(): string
    {
        return $this->getPostalCode().' '.$this->getCity().' ('.$this->getState().')';
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getFiscalName().($this->commercialName ? ' ('.$this->getCommercialName().')' : '') : self::DEFAULT_NULL_STRING;
    }
}
