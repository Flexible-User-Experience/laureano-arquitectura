<?php

namespace App\Enum;

enum LanguageEnum: string
{
    case CATALAN = 'ca';
    case SPANISH = 'es';
    case ENGLISH = 'en';

    public static function getChoices(): array
    {
        return [
            'catalan' => self::CATALAN->value,
            'spanish' => self::SPANISH->value,
            'english' => self::ENGLISH->value,
        ];
    }
}
