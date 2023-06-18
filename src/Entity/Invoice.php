<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\HasBeenPaidTrait;
use App\Entity\Traits\PaymentDateTrait;
use App\Entity\Traits\TaxBaseTrait;
use App\Entity\Traits\TaxPercentageTrait;
use App\Entity\Traits\TotalTrait;
use App\Enum\PaymentMethodEnum;
use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\UniqueConstraint(name: 'series_number', columns: ['series', 'number'])]
#[UniqueEntity(fields: ['series', 'number'])]
class Invoice extends AbstractBase
{
    use DateTrait;
    use HasBeenPaidTrait;
    use PaymentDateTrait;
    use TaxBaseTrait;
    use TaxPercentageTrait;
    use TotalTrait;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'invoices')]
    private Customer $customer;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $date;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sendDate = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $hasBeenSent = false;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => PaymentMethodEnum::TRANSFER])]
    private int $paymentMethod = PaymentMethodEnum::TRANSFER;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $paymentComments = null;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['default' => self::DEFAULT_NULL_STRING])]
    private string $series = self::DEFAULT_NULL_STRING;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $number = 0;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $isIntraCommunityInvoice = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $customerReference = null;

    #[ORM\Column(type: Types::FLOAT, precision: 2, options: ['default' => 0.00])]
    private float $discountPercentage = 0.00;

    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceLine::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private ?Collection $invoiceLines;

    public function __construct()
    {
        $this->invoiceLines = new ArrayCollection();
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSendDate(): ?\DateTimeInterface
    {
        return $this->sendDate;
    }

    public function setSendDate(?\DateTimeInterface $sendDate): self
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    public function hasBeenSent(): bool
    {
        return $this->hasBeenSent;
    }

    public function getHasBeenSent(): bool
    {
        return $this->hasBeenSent();
    }

    public function setHasBeenSent(bool $hasBeenSent): self
    {
        $this->hasBeenSent = $hasBeenSent;

        return $this;
    }

    public function getPaymentMethod(): int
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(int $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPaymentComments(): ?string
    {
        return $this->paymentComments;
    }

    public function setPaymentComments(?string $paymentComments): self
    {
        $this->paymentComments = $paymentComments;

        return $this;
    }

    public function getSeries(): string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getSerialNumber(): string
    {
        return $this->getSeries().self::DEFAULT_SERIAL_NUMBER_SEPARATOR.$this->getNumber();
    }

    public function getSluggedSerialNumber(): string
    {
        return $this->getSeries().'-'.sprintf('%03d', $this->getNumber());
    }

    public function getSluggedSerialNumberWithName(): string
    {
        return $this->getSluggedSerialNumber().' · '.$this->getCustomer()->getCommercialOrFiscalName();
    }

    public function isIntraCommunityInvoice(): bool
    {
        return $this->isIntraCommunityInvoice;
    }

    public function getIsIntraCommunityInvoice(): bool
    {
        return $this->isIntraCommunityInvoice();
    }

    public function setIsIntraCommunityInvoice(bool $isIntraCommunityInvoice): self
    {
        $this->isIntraCommunityInvoice = $isIntraCommunityInvoice;

        return $this;
    }

    public function getCustomerReference(): ?string
    {
        return $this->customerReference;
    }

    public function setCustomerReference(?string $customerReference): Invoice
    {
        $this->customerReference = $customerReference;

        return $this;
    }

    public function getDiscountPercentage(): float
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(float $discountPercentage): self
    {
        $this->discountPercentage = $discountPercentage;

        return $this;
    }

    public function getInvoiceLines(): Collection
    {
        return $this->invoiceLines;
    }

    public function addInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if (!$this->invoiceLines->contains($invoiceLine)) {
            $invoiceLine->setInvoice($this);
            $this->invoiceLines->add($invoiceLine);
        }

        return $this;
    }

    public function removeInvoiceLine(InvoiceLine $invoiceLine): self
    {
        if ($this->invoiceLines->contains($invoiceLine)) {
            $this->invoiceLines->removeElement($invoiceLine);
        }

        return $this;
    }

    public function calculateBaseAmount(): Money
    {
        $result = new Money(0, new Currency(self::DEFAULT_CURRENCY_STRING));
        /** @var InvoiceLine $line */
        foreach ($this->getInvoiceLines() as $line) {
            $result = $result->add($line->calculateBaseAmount());
        }

        return $result;
    }

    public function calculateDiscountBaseAmount(): Money
    {
        $result = $this->calculateBaseAmount();

        return $result->multiply($this->getDiscountPercentage())->divide(100);
    }

    public function calculateTaxBaseAmount(): Money
    {
        $result = $this->calculateBaseAmount();
        $result = $result->subtract($this->calculateDiscountBaseAmount());

        return $result->multiply($this->getTaxPercentage())->divide(100);
    }

    public function calculateTotalBaseAmount(): Money
    {
        $result = new Money(0, new Currency(self::DEFAULT_CURRENCY_STRING));
        /** @var InvoiceLine $line */
        foreach ($this->getInvoiceLines() as $line) {
            $result = $result->add($line->calculateBaseAmount());
        }
        $discountAmount = $result->multiply($this->getDiscountPercentage())->divide(100);
        $resultMinusDiscount = $result->subtract($discountAmount);
        $this->setTaxBase($resultMinusDiscount);
        $resultTax = $resultMinusDiscount->multiply($this->getTaxPercentage())->divide(100);
        $this->setTotal($resultMinusDiscount->add($resultTax));

        return $result;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getSluggedSerialNumberWithName() : self::DEFAULT_NULL_STRING.self::DEFAULT_SERIAL_NUMBER_SEPARATOR.self::DEFAULT_NULL_STRING;
    }
}
