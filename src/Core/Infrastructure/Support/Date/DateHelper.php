<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Date;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Throwable;

class DateHelper
{
    public static function compare($date1, $operator, $date2): ?bool
    {
        if (is_null($date1) || is_null($date2)) {
            return null;
        }
        $timestamp_date1 = CarbonImmutable::parse($date1)->timestamp;
        $timestamp_date2 = CarbonImmutable::parse($date2)->timestamp;
        return match ($operator) {
            '>'  => $timestamp_date1 >  $timestamp_date2,
            '<'  => $timestamp_date1 <  $timestamp_date2,
            '>=' => $timestamp_date1 >= $timestamp_date2,
            '<=' => $timestamp_date1 <= $timestamp_date2,
            '==' => $timestamp_date1 == $timestamp_date2,
            '!=' => $timestamp_date1 != $timestamp_date2,
            default => throw new \InvalidArgumentException('Invalid operator: ' . $operator),
        };
    }

    public static function mergeDateAndTime(string|CarbonImmutable $date, string|CarbonImmutable $time): ?CarbonImmutable
    {
        $date = ($date instanceof CarbonImmutable) ? $date : CarbonImmutable::parse($date);
        $time = ($time instanceof CarbonImmutable) ? $time : CarbonImmutable::parse($time);
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
