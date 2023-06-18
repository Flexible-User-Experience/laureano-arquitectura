<?php

namespace App\Enum;

enum ExpenseCategoryEnum: string
{
    case OTHER = 'OTHER';
    case CONSUMABLE = 'CONSUMABLE';
    case INSURANCE = 'INSURANCE';
    case MANAGEMENT = 'MANAGEMENT';
    case MAINTENANCE = 'MAINTENANCE';

    public static function getChoices(): array
    {
        return [
            'Other' => self::OTHER->value,
            'Consumable' => self::CONSUMABLE->value,
            'Insurance' => self::INSURANCE->value,
            'Management' => self::MANAGEMENT->value,
            'Maintenance' => self::MAINTENANCE->value,
        ];
    }
}
