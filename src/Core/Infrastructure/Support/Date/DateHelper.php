<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Date;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonInterval;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Throwable;

class DateHelper
{
    public static function compare(
        string|CarbonInterface|null $date1,
        string $operator,
        string|CarbonInterface|null $date2
    ): ?bool
    {
        if (is_null($date1) || is_null($date2)) {
            return null;
        }
        $date1 = $date1 instanceof CarbonInterface ? $date1 : CarbonImmutable::parse($date1);
        $date2 = $date2 instanceof CarbonInterface ? $date2 : CarbonImmutable::parse($date2);
        return match ($operator) {
            '>'  => $date1 >  $date2,
            '<'  => $date1 <  $date2,
            '>=' => $date1 >= $date2,
            '<=' => $date1 <= $date2,
            '==' => $date1 == $date2,
            '!=' => $date1 != $date2,
            default => throw new \InvalidArgumentException('Invalid operator: ' . $operator),
        };
    }

    public static function mergeDateAndTime(string|CarbonInterface $date, string|CarbonInterface $time): CarbonImmutable
    {
        $date = ($date instanceof CarbonInterface) ? $date : CarbonImmutable::parse($date);
        $time = ($time instanceof CarbonInterface) ? $time : CarbonImmutable::parse($time);
        $date = $date->format(DateFormat::date_startYear->value);
        $time = $time->format(DateFormat::time->value);
        return CarbonImmutable::parse($date . ' ' . $time);
    }

    public static function matchesFormat(string $date, string $format): bool
    {
        try {
            $dt = CarbonImmutable::createFromFormat($format, $date);
            return $dt !== false && $dt->format($format) === $date;
        } catch (Throwable $e) {
            return false;
        }
    }

    public static function matchesAnyFormat(string $date, array $formats): bool
    {
        // Iterar sobre cada formato y llamar a checkFormat
        foreach ($formats as $format) {
            if (static::matchesFormat($date, $format)) {
                return true;
            }
        }

        // Si ningún formato coincide, retornar false
        return false;
    }

    /**
     * Measures the execution time of a callback and returns it as a CarbonInterval.
     * You can format the result using CarbonInterval's formatting methods, for example: ->format("%I min %S sec, %f ms")
     *
     * @param callable $callback
     * @return CarbonInterval
     */
    public static function measure(callable $callback): CarbonInterval
    {
        $init = CarbonImmutable::now();
        $callback();
        $end      = CarbonImmutable::now();
        return $init->diff($end);
    }
}
