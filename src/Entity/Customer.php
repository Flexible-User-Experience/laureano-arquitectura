<?php

namespace App\Entity;

use App\Entity\Traits\TotalInvoicedTrait;
use App\Enum\LocaleEnum;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'customer')]
#[UniqueEntity(fields: ['fiscalIdentificationCode'])]
class Customer extends AbstractAccount
{
    use TotalInvoicedTrait;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoice::class, cascade: ['persist', 'remove'])]
    protected Collection $invoices;

    #[ORM\Column(type: Types::STRING, length: 20, unique: true)]
    protected string $fiscalIdentificationCode;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $fiscalName;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $commercialName = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $address1 = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $address2 = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $postalCode = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $city = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $state = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $country = null;

    #[Assert\Email]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $phoneNumber = null;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => true])]
    protected bool $isCompany = true;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->locale = LocaleEnum::CA->value;
    }

    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function setInvoices(Collection $invoices): self
    {
        $this->invoices = $invoices;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getFiscalName() : AbstractBase::DEFAULT_NULL_STRING;
    }
}
