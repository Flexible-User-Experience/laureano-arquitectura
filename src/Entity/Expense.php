<?php

namespace App\Entity;

use App\Entity\Traits\DateTrait;
use App\Entity\Traits\DescriptionTrait;
use App\Entity\Traits\HasBeenPaidTrait;
use App\Entity\Traits\PaymentDateTrait;
use App\Entity\Traits\TaxBaseTrait;
use App\Entity\Traits\TaxPercentageTrait;
use App\Entity\Traits\TotalTrait;
use App\Manager\AssetsManager;
use App\Repository\ExpenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[Vich\Uploadable]
class Expense extends AbstractBase
{
    use DateTrait;
    use DescriptionTrait;
    use HasBeenPaidTrait;
    use PaymentDateTrait;
    use TaxBaseTrait;
    use TaxPercentageTrait;
    use TotalTrait;

    #[ORM\ManyToOne(targetEntity: ExpenseCategory::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?ExpenseCategory $expenseCategory = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\File(mimeTypes: [AssetsManager::MIME_APPLICATION_PDF_TYPE, AssetsManager::MIME_APPLICATION_PDF_X_TYPE])]
    #[Vich\UploadableField(mapping: 'expenses', fileNameProperty: 'documentFilename')]
    private ?File $document = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $documentFilename = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    private ?Provider $provider = null;

    public function getExpenseCategory(): ?ExpenseCategory
    {
        return $this->expenseCategory;
    }

    public function setExpenseCategory(?ExpenseCategory $expenseCategory): self
    {
        $this->expenseCategory = $expenseCategory;

        return $this;
    }

    public function getDocument(): ?File
    {
        return $this->document;
    }

    public function setDocument(?File $document): self
    {
        $this->document = $document;
        if (null !== $document) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getDocumentFilename(): ?string
    {
        return $this->documentFilename;
    }

    public function setDocumentFilename(?string $documentFilename): self
    {
        $this->documentFilename = $documentFilename;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function calculateTotalBaseAmount(): Money
    {
        $result = new Money(0, new Currency(self::DEFAULT_CURRENCY_STRING));
        $resultTax = $this->getTaxBase()->multiply($this->getTaxPercentage())->divide(100);
        $this->setTotal($this->getTaxBase()->add($resultTax));

        return $result;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getDateString().' · '.$this->getDescription() : self::DEFAULT_NULL_STRING;
    }
}
