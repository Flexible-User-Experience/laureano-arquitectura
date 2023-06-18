<?php

namespace App\Enum;

class ReceiptYearMonthEnum
{
    public const APP_FIRST_YEAR = 2020;

    public const JANUARY = 1;
    public const FEBRUARY = 2;
    public const MARCH = 3;
    public const APRIL = 4;
    public const MAY = 5;
    public const JUNE = 6;
    public const JULY = 7;
    public const AUGUST = 8;
    public const SEPTEMBER = 9;
    public const OCTOBER = 10;
    public const NOVEMBER = 11;
    public const DECEMBER = 12;

    public static function getMonthEnumArray(): array
    {
        return array_flip(self::getReversedMonthEnumArray());
    }

    public static function getReversedMonthEnumArray(): array
    {
        return [
            self::JANUARY => 'month.january',
            self::FEBRUARY => 'month.february',
            self::MARCH => 'month.march',
            self::APRIL => 'month.april',
            self::MAY => 'month.may',
            self::JUNE => 'month.june',
            self::JULY => 'month.july',
            self::AUGUST => 'month.august',
            self::SEPTEMBER => 'month.september',
            self::OCTOBER => 'month.october',
            self::NOVEMBER => 'month.november',
            self::DECEMBER => 'month.december',
        ];
    }

    public static function getTranslatedMonthEnumArray(): array
    {
        return [
            self::JANUARY => 'gener',
            self::FEBRUARY => 'febrer',
            self::MARCH => 'març',
            self::APRIL => 'abril',
            self::MAY => 'maig',
            self::JUNE => 'juny',
            self::JULY => 'juliol',
            self::AUGUST => 'agost',
            self::SEPTEMBER => 'setembre',
            self::OCTOBER => 'octubre',
            self::NOVEMBER => 'novembre',
            self::DECEMBER => 'desembre',
        ];
    }

    public static function getShortTranslatedMonthEnumArray(): array
    {
        return [
            self::JANUARY => 'gen',
            self::FEBRUARY => 'feb',
            self::MARCH => 'mar',
            self::APRIL => 'abr',
            self::MAY => 'mai',
            self::JUNE => 'jun',
            self::JULY => 'jul',
            self::AUGUST => 'ago',
            self::SEPTEMBER => 'set',
            self::OCTOBER => 'oct',
            self::NOVEMBER => 'nov',
            self::DECEMBER => 'des',
        ];
    }

    public static function getYearEnumArray(): array
    {
        return array_flip(self::getReversedYearEnumArray());
    }

    public static function getReversedYearEnumArray(): array
    {
        $result = [];
        $now = new \DateTimeImmutable();
        $currentYear = (int) $now->format('Y');
        if (12 === (int) $now->format('m') && 15 < (int) $now->format('d')) {
            ++$currentYear;
        }
        $steps = $currentYear - self::APP_FIRST_YEAR + 1;
        for ($i = 0; $i < $steps; ++$i) {
            $year = $currentYear - $i;
            $result[(string) $year] = $year;
        }

        return $result;
    }
}
