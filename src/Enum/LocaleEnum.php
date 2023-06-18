<?php

namespace App\Enum;

enum LocaleEnum: string
{
    case CA = 'ca';
    case ES = 'es';
    case EN = 'en';

    public static function getChoices(): array
    {
        return [
            'catalan' => self::CA->value,
            'spanish' => self::ES->value,
            'english' => self::EN->value,
        ];
    }
}
