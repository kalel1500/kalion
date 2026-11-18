<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Date;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use DateTimeInterface;
use InvalidArgumentException;
use Throwable;

class DateHelper
{
    /** Unescaped PHP format characters that represent a date part (day, month, year or full timestamp). */
    private const DATE_TOKENS = 'dDjlNSwzWFmMntLoXxYyU';

    /**
     * Creates a date from a string using the exact given format, interpreting it in the timezone the format implies
     * (`UTC` for `\Z`, application timezone otherwise). Unlike `CarbonImmutable::parse()`, ambiguous formats such as
     * `d/m/Y` are never interpreted as `m/d/Y`.
     *
     * Fields not present in a format containing a date part are reset (e.g. `Y-m-d` => `00:00:00`, `m/Y` => day 1).
     * Time-only formats (e.g. `H:i:s`) keep the current date, like `CarbonImmutable::parse()`.
     *
     * @throws InvalidArgumentException if the string does not match the format
     */
    public static function createFromFormat(string $format, string $date): CarbonImmutable
    {
        try {
            $dt = CarbonImmutable::createFromFormat(static::normalizeParseFormat($format), $date, static::timezoneForFormat($format));
        } catch (Throwable $e) {
            throw new InvalidArgumentException(sprintf('The date <%s> does not match the format <%s>.', $date, $format), 0, $e);
        }

        if ($dt === false) {
            throw new InvalidArgumentException(sprintf('The date <%s> does not match the format <%s>.', $date, $format));
        }

        return $dt;
    }

    /**
     * Compares two dates as instants (the timezone of each operand does not affect the result).
     * Strings without timezone information are interpreted in the application timezone.
     */
    public static function compare(
        string|DateTimeInterface|null $date1,
        string $operator,
        string|DateTimeInterface|null $date2
    ): ?bool
    {
        if (is_null($date1) || is_null($date2)) {
            return null;
        }
        $date1 = static::toImmutable($date1);
        $date2 = static::toImmutable($date2);
        return match ($operator) {
            '>'  => $date1 >  $date2,
            '<'  => $date1 <  $date2,
            '>=' => $date1 >= $date2,
            '<=' => $date1 <= $date2,
            '==' => $date1 == $date2,
            '!=' => $date1 != $date2,
            default => throw new InvalidArgumentException('Invalid operator: ' . $operator),
        };
    }

    /**
     * Combines the calendar date of `$date` with the wall-clock time (H:i:s) of `$time`.
     *
     * Both values are first normalized to the application timezone (`date_default_timezone_get()`),
     * so that an instant serialized in UTC (e.g. `2026-03-29T23:30:00.000000Z`) contributes its local date/time.
     * The result is always expressed in the application timezone.
     */
    public static function mergeDateAndTime(string|DateTimeInterface $date, string|DateTimeInterface $time): CarbonImmutable
    {
        $appTz = date_default_timezone_get();
        $date  = static::toImmutable($date)->tz($appTz);
        $time  = static::toImmutable($time)->tz($appTz);

        return $date->setTime($time->hour, $time->minute, $time->second);
    }

    /**
     * Purely syntactic check: the string must round-trip exactly through the given format.
     *
     * The check is done in UTC (no DST), so wall-clock times that do not exist in the application timezone
     * (e.g. `2026-03-29 02:30:00` in `Europe/Madrid`) are not reported as invalid formats.
     * Formats that carry their own offset (`P`, `O`, `e`, `T`...) are not affected, since the parsed offset prevails.
     */
    public static function matchesFormat(string $date, string $format): bool
    {
        try {
            $dt = CarbonImmutable::createFromFormat(static::normalizeParseFormat($format), $date, 'UTC');
            return $dt !== false && $dt->format($format) === $date;
        } catch (Throwable) {
            return false;
        }
    }

    public static function matchesAnyFormat(string $date, array $formats): bool
    {
        return ! is_null(static::firstMatchingFormat($date, $formats));
    }

    public static function firstMatchingFormat(string $date, array $formats): ?string
    {
        foreach ($formats as $format) {
            if (static::matchesFormat($date, $format)) {
                return $format;
            }
        }
        return null;
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

    /**
     * Returns the timezone in which a string with the given format is expressed:
     * `UTC` if the format contains the literal Zulu designator (`\Z`), otherwise the application timezone.
     *
     * Note: an unescaped `Z` is the PHP token for the offset in seconds (the string carries its own offset),
     * so only the escaped literal `\Z` is considered a UTC marker.
     */
    public static function timezoneForFormat(string $format): string
    {
        return static::isUtcFormat($format) ? 'UTC' : date_default_timezone_get();
    }

    /**
     * Whether the format contains the literal Zulu designator (`\Z`), meaning the string is expressed in UTC.
     */
    public static function isUtcFormat(string $format): bool
    {
        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                if (($format[$i + 1] ?? null) === 'Z') {
                    return true;
                }
                $i++; // skip escaped char
            }
        }
        return false;
    }

    protected static function normalizeParseFormat(string $format): string
    {
        if (str_contains($format, '!') || str_contains($format, '|')) {
            return $format;
        }

        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            if ($format[$i] === '\\') {
                $i++;
                continue;
            }
            if (str_contains(self::DATE_TOKENS, $format[$i])) {
                return $format . '|';
            }
        }

        return $format;
    }

    protected static function toImmutable(string|DateTimeInterface $value): CarbonImmutable
    {
        return $value instanceof DateTimeInterface ? CarbonImmutable::instance($value) : CarbonImmutable::parse($value);
    }

}
