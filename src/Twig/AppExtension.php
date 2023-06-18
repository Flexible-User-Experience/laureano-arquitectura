<?php

namespace App\Twig;

use App\Entity\AbstractBase;
use Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

class AppExtension extends AbstractExtension
{

    /**
     * Twig Tests.
     */
    public function getTests(): array
    {
        return [
            new TwigTest('instance_of', [$this, 'isInstanceOf']),
        ];
    }

    public function isInstanceOf($var, $instance): bool
    {
        return (new \ReflectionClass($instance))->isInstance($var);
    }

    /**
     * Twig Functions.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('generate_random_error_text', [$this, 'generateRandomErrorText']),
            new TwigFunction('get_semaphore_text_color_class', [$this, 'getSemaphoreTextColorClass']),
        ];
    }

    /**
     * @throws \Exception
     */
    public function generateRandomErrorText(int $length = 1024): string
    {
        // Character List to Pick from
        $chrList = '012 3456 789 abcdef ghij klmno pqrs tuvwxyz ABCD EFGHIJK LMN OPQ RSTU VWXYZ';
        // Minimum/Maximum times to repeat character List to seed from
        $chrRepeatMin = 1; // Minimum times to repeat the seed string
        $chrRepeatMax = 30; // Maximum times to repeat the seed string

        return substr(str_shuffle(str_repeat($chrList, random_int($chrRepeatMin, $chrRepeatMax))), 1, $length);
    }

    public function getSemaphoreTextColorClass(float $value): string
    {
        $result = 'danger';
        if ($value >= 65 && $value < 75) {
            $result = 'warning';
        } elseif ($value >= 75) {
            $result = 'success';
        }

        return $result;
    }

    /**
     * Twig Filters.
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('hm', [$this, 'datetimeAsHourMinuteString']),
            new TwigFilter('i', [$this, 'integerAsString']),
            new TwigFilter('f', [$this, 'floatAsString']),
            new TwigFilter('m', [$this, 'moneyAsIntegerEuro']),
            new TwigFilter('percentage', [$this, 'floatAsPercentage']),
            new TwigFilter('eur', [$this, 'floatAsEuro']),
        ];
    }

    public function datetimeAsHourMinuteString(\DateTimeInterface $value): string
    {
        return $value->format('H:i');
    }

    public function integerAsString(float $value): string
    {
        return number_format($value, 0, ',', '.');
    }

    public function floatAsString(float $value): string
    {
        return number_format($value, 2, ',', '.');
    }

    public function moneyAsIntegerEuro(Money $value): string
    {
        $intValue = (int) ((int) $value->getAmount() / 100);

        return $this->integerAsString($intValue).' '.AbstractBase::DEFAULT_CURRENCY_SYMBOL;
    }

    public function floatAsPercentage(float $value): string
    {
        return number_format($value, 0, ',', '.').' %';
    }

    public function floatAsEuro(float $value): string
    {
        return $this->floatAsString($value).' '.AbstractBase::DEFAULT_CURRENCY_SYMBOL;
    }
}
