<?php

namespace App\Entity;

use App\Entity\Traits\NameTrait;
use App\Entity\Traits\TotalInvoicedTrait;
use App\Repository\ExpenseCategoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExpenseCategoryRepository::class)]
class ExpenseCategory extends AbstractBase
{
    use NameTrait;
    use TotalInvoicedTrait;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private string $name;

    public function __toString(): string
    {
        return $this->id ? $this->getName() : self::DEFAULT_NULL_STRING;
    }
}
