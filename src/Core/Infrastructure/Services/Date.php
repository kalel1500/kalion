<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Throwable;

final class Date
{
    public static function stringToformat($date, $format, $getNowIfNullRecived = false): ?string
    {
        $isInValid = (is_null($date) || $date === '');
        $returnNow = $getNowIfNullRecived;

        if ($isInValid && ! $returnNow) {
            return null;
        }

        if ($isInValid && $returnNow) {
            $carbon = static::now();
        } else {
            $carbon = static::parse($date);
        }

        return $carbon->format($format);
    }

    public static function formatInputDateToAudit($imputDate): ?string
    {
        return Date::stringToformat($imputDate, DateFormat::datetime_startYear->value);
    }

    public static function parse($date): CarbonImmutable
    {
        return CarbonImmutable::parse($date);
    }

    public static function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }

    public static function compare($date1, $operator, $date2)
    {
        if (is_null($date1) || is_null($date2)) {
            return null;
        }
        $timestamp_date1 = static::parse($date1)->timestamp;
        $timestamp_date2 = static::parse($date2)->timestamp;
        $operation       = $timestamp_date1 . $operator . $timestamp_date2;
        return (eval('return ' . $operation . ';'));
    }

    public static function mergeDateAndTime(string|CarbonImmutable $date, string|CarbonImmutable $time): ?CarbonImmutable
    {
        $date = ($date instanceof CarbonImmutable) ? $date : static::parse($date);
        $time = ($time instanceof CarbonImmutable) ? $time : static::parse($time);
        $date = $date->format(DateFormat::date_startYear->value);
        $time = $time->format(DateFormat::time->value);
        return static::parse($date . ' ' . $time);
    }

    public static function checkFormat(string $date, string $format, bool $allowZeros = false): bool
    {
        // Verificar si el valor es cero según el formato y $allowZeros es true
        if ($allowZeros && static::isZeroDate($date, $format)) {
            return true;
        }

        // Verificar el formato utilizando CarbonImmutable
        try {
            $formatted = CarbonImmutable::parse($date)->format($format);
            return ($date === $formatted);
        } catch (Throwable $e) {
            // Si el parseo falla, retornar false
            return false;
        }
    }

    public static function checkFormats(string $date, array $formats, bool $allowZeros = false): bool
    {
        // Iterar sobre cada formato y llamar a checkFormat
        foreach ($formats as $format) {
            if (static::checkFormat($date, $format, $allowZeros)) {
                return true;
            }
        }

        // Si ningún formato coincide, retornar false
        return false;
    }

    private static function isZeroDate(string $date, string $format): bool
    {
        // Crear una cadena de ceros basada en el formato
        $zeroDate = strtr($format, [
            'Y' => '0000',
            'm' => '00',
            'd' => '00',
            'H' => '00',
            'i' => '00',
            's' => '00',
        ]);

        return $date === $zeroDate;
    }

    public static function debugTime(string $debugTitle, callable $callback)
    {
        dump($debugTitle);
        $init = Date::now();
        $callback();
        $end      = Date::now();
        $interval = $init->diff($end);
        dump($init->format('H:i:s'));
        dump($end->format('H:i:s'));
        dump($interval->format("%I min %S sec, %f ms"));
        dd('fin');
    }
}
