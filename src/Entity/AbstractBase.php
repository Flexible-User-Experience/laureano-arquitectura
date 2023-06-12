<?php

namespace App\Entity;

use App\Enum\BooleanEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

abstract class AbstractBase
{
    public const DEFAULT_VALUE_ADDED_TAX = 21.00;
    public const DEFAULT_CURRENCY_STRING = 'EUR';
    public const DEFAULT_CURRENCY_SYMBOL = '€';
    public const DEFAULT_SERIAL_NUMBER_SEPARATOR = '/';
    public const DEFAULT_NULL_STRING = '---';
    public const DEFAULT_NULL_DATE_STRING = '--/--/----';
    public const DEFAULT_NULL_DATETIME_STRING = '--/--/---- --:--';
    public const DATE_PICKER_TYPE_FORMAT = 'dd/MM/yyyy';
    public const DATE_FORM_TYPE_FORMAT = 'd/M/y';
    public const DATE_STRING_FORMAT = 'd/m/Y';
    public const DATETIME_STRING_FORMAT = 'd/m/Y H:i';
    public const DATABASE_DATE_STRING_FORMAT = 'Y-m-d';
    public const DATABASE_DATETIME_STRING_FORMAT = 'Y-m-d H:i:s';

    #[ORM\Column]
    #[ORM\GeneratedValue]
    #[ORM\Id]
    protected ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, unique: true, nullable: true)]
    protected ?int $legacyId = null;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $updatedAt;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    protected bool $active = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegacyId(): ?int
    {
        return $this->legacyId;
    }

    public function setLegacyId(?int $legacyId): self
    {
        $this->legacyId = $legacyId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getCreatedAtString(): string
    {
        return self::convertDateTimeAsString($this->getCreatedAt());
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getUpdatedAtString(): string
    {
        return self::convertDateTimeAsString($this->getUpdatedAt());
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getActive(): ?bool
    {
        return $this->isActive();
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public static function convertBooleanValueAsString(?bool $value): string
    {
        return $value ? BooleanEnum::YES->value : BooleanEnum::NO->value;
    }

    public static function convertFloatAsString(?float $value): string
    {
        return $value ? number_format($value, 2, ',', '.') : self::DEFAULT_NULL_STRING;
    }

    public static function convertDateAsString(?\DateTimeInterface $date): string
    {
        return $date ? $date->format(self::DATE_STRING_FORMAT) : self::DEFAULT_NULL_DATE_STRING;
    }

    public static function convertDateTimeAsString(?\DateTimeInterface $date): string
    {
        return $date ? $date->format(self::DATETIME_STRING_FORMAT) : self::DEFAULT_NULL_DATETIME_STRING;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getId().' · '.$this->getCreatedAtString() : self::DEFAULT_NULL_STRING;
    }
}
