<?php

namespace App\Entity;

use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\TotalTrait;
use App\Entity\Traits\UnitPriceTrait;
use App\Repository\InvoiceLineRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;

#[ORM\Entity(repositoryClass: InvoiceLineRepository::class)]
class InvoiceLine extends AbstractBase
{
    use DescriptionTrait;
    use TotalTrait;
    use UnitPriceTrait;

    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'invoiceLines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invoice $invoice = null;

    #[ORM\Column(type: Types::FLOAT, precision: 2, options: ['default' => 0.00])]
    private float $units = 0.00;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT, precision: 2, nullable: true)]
    private ?float $discount = null;

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(?Invoice $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function calculateBaseAmount(): Money
    {
        $total = $this->getUnitPrice()->multiply($this->getUnits() * 100)->divide(100);
        $this->setTotal($total);

        return $total;
    }
}
