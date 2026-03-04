<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Date;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
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

    public static function mergeDateAndTime(string|CarbonInterface $date, string|CarbonInterface $time): ?CarbonImmutable
    {
        $date = ($date instanceof CarbonInterface) ? $date : CarbonImmutable::parse($date);
        $time = ($time instanceof CarbonInterface) ? $time : CarbonImmutable::parse($time);
        $date = $date->format(DateFormat::date_startYear->value);
        $time = $time->format(DateFormat::time->value);
        return CarbonImmutable::parse($date . ' ' . $time);
    }

    public static function checkFormat(string $date, string $format): bool
    {
        // Verificar el formato utilizando CarbonImmutable
        try {
            $formatted = CarbonImmutable::parse($date)->format($format);
            return ($date === $formatted);
        } catch (Throwable $e) {
            // Si el parseo falla, retornar false
            return false;
        }
    }

    public static function checkFormats(string $date, array $formats): bool
    {
        // Iterar sobre cada formato y llamar a checkFormat
        foreach ($formats as $format) {
            if (static::checkFormat($date, $format)) {
                return true;
            }
        }

        // Si ningún formato coincide, retornar false
        return false;
    }

    public static function debugTime(string $debugTitle, callable $callback)
    {
        dump($debugTitle);
        $init = CarbonImmutable::now();
        $callback();
        $end      = CarbonImmutable::now();
        $interval = $init->diff($end);
        dump($init->format('H:i:s'));
        dump($end->format('H:i:s'));
        dump($interval->format("%I min %S sec, %f ms"));
        dd('fin');
    }
}
