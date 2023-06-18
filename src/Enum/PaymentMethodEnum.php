<?php

namespace App\Enum;

enum PaymentMethodEnum: int
{
    public const TRANSFER = 0;
    public const DRAFT = 1;
    public const MONEY = 2;

    case BANK_TRANSFER = self::TRANSFER;
    case BANK_DRAFT = self::DRAFT;
    case CASH = self::MONEY;
}
