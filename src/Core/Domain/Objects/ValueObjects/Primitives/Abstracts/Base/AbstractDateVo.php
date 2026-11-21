<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use DateTimeZone;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Domain\Support\DateHelper;
use Throwable;

/**
 * Date Value Object: an immutable (and optionally nullable) wrapper of `CarbonImmutable`.
 *
 * - `$value` is a `CarbonImmutable` (or `null`), always expressed in the timezone given when creating the VO
 *   (`$tz`) or, by default, in the application timezone (`date_default_timezone_get()`), regardless of the origin
 *   of the data (for example, `2026-03-29T02:30:00.000000Z` and `2026-03-29 04:30:00.000000` are the same date
 *   with `Europe/Madrid`).
 * - `from()` validates that the string matches one of the allowed formats (`static::$formats`) or the format produced
 *   by `jsonSerialize()`: use it to read from the database, to rebuild an object from its own `toArray()` or to check
 *   whether a string is a valid date.
 * - `parse()` accepts any value understood by `CarbonImmutable::parse()`: use it with dates received from the front.
 * - `jsonSerialize()` (and therefore `toArray()` of entities, collections and DTOs) returns the serialization format
 *   (`kalion.dates.serialization_format` and `kalion.dates.serialize_to_local_tz`) and `toDb()` (used by `toArrayDb()`
 *   of the entities) the storage format (the first of `static::$formats`).
 * - The methods of Carbon can be called directly on the VO (`$vo->toDateTimeString()`, `$vo->diffForHumans()`...).
 *   They return `null` when the value is `null`.
 *
 * @mixin CarbonImmutable
 */
abstract class AbstractDateVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    /**
     * Formats allowed by `from()`. The first one is the storage format (`toDb()`).
     */
    protected static array $formats = [DateFormat::datetime_YMD->value];

    /**
     * @var CarbonImmutable|null
     */
    public $value;

    /**
     * @param DateTimeInterface|null   $value The date (it is never modified, even if it is mutable)
     * @param DateTimeZone|string|null $tz    Timezone of the VO (default: application timezone)
     */
    public function __construct(?DateTimeInterface $value, DateTimeZone|string|null $tz = null)
    {
        $this->checkNullable($value);
        $this->value = is_null($value) ? null : CarbonImmutable::instance($value)->tz($tz ?? date_default_timezone_get());
    }

    /**
     * Creates the VO validating that the string matches one of the allowed formats.
     *
     * By default, the allowed formats are `static::$formats` and the serialization format (`serializationFormat()`),
     * so an object can always be rebuilt from its own `toArray()`/`jsonSerialize()`. Strings matching
     * `static::$formats` without timezone information are interpreted in `$tz` (default: application timezone),
     * strings matching the serialization format in the serialization timezone, and strings with the Zulu
     * designator (`\Z`) in `UTC`.
     *
     * @param string|DateTimeInterface|null $value
     * @param string[]|null                 $formats Allowed formats (default: `static::$formats` and the serialization format)
     * @param DateTimeZone|string|null      $tz      Timezone of the VO (default: application timezone)
     * @throws InvalidValueException if the value is not a string matching one of the allowed formats
     */
    public static function from($value, ?array $formats = null, DateTimeZone|string|null $tz = null): static
    {
        if (is_null($value) || $value instanceof DateTimeInterface) {
            return new static($value, $tz);
        }

        $allowed = $formats ?? static::$formats;
        $format  = is_string($value) ? DateHelper::firstMatchingFormat($value, $allowed) : null;
        $parseTz = $tz;

        if (is_null($formats)) {
            $serializationFormat = static::serializationFormat();
            $allowed             = array_values(array_unique([...$allowed, $serializationFormat]));

            if (is_null($format) && is_string($value) && DateHelper::matchesFormat($value, $serializationFormat)) {
                $format  = $serializationFormat;
                $parseTz = static::serializationTimezone();
            }
        }

        if (is_null($format)) {
            $printable = is_scalar($value) ? (string)$value : get_debug_type($value);
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $printable, implode(', ', $allowed)));
        }

        return new static(DateHelper::createFromFormat($format, $value, $parseTz), $tz);
    }

    /**
     * Creates the VO from any value understood by `CarbonImmutable::parse()` (without format validation).
     *
     * Strings without timezone information are interpreted in `$tz` (default: application timezone).
     *
     * @param string|DateTimeInterface|null $value
     * @param DateTimeZone|string|null      $tz Timezone of the VO (default: application timezone)
     * @throws InvalidValueException if the value can not be parsed as a date
     */
    public static function parse($value, DateTimeZone|string|null $tz = null): static
    {
        if (is_null($value) || $value instanceof DateTimeInterface) {
            return new static($value, $tz);
        }

        try {
            $date = CarbonImmutable::parse((string)$value, $tz ?? date_default_timezone_get());
        } catch (Throwable) {
            $printable = is_scalar($value) ? (string)$value : get_debug_type($value);
            throw new InvalidValueException(sprintf('<%s> can not parse the value <%s> as a date.', class_basename(static::class), $printable));
        }

        return new static($date, $tz);
    }

    /**
     * Returns a new instance converted to the given timezone (the instant does not change).
     */
    public function tz(DateTimeZone|string $timezone): static
    {
        $clone        = clone $this;
        $clone->value = $this->value?->tz($timezone);
        return $clone;
    }

    /**
     * Alias of `tz()`.
     */
    public function timezone(DateTimeZone|string $timezone): static
    {
        return $this->tz($timezone);
    }

    /**
     * Returns a new instance converted to `UTC`. Equivalent to `->tz('UTC')`.
     */
    public function utc(): static
    {
        return $this->tz('UTC');
    }

    /**
     * Returns a new instance converted to the timezone used to present dates to the users
     * (`kalion.dates.local_timezone` or, if it is not configured, Laravel's `app.timezone`).
     */
    public function local(): static
    {
        return $this->tz(DateHelper::localTimezone());
    }

    /**
     * Keeps the timezone of the instance.
     */
    public function toNull()
    {
        $class = static::CLASS_NULLABLE;
        return new $class($this->value, $this->value?->getTimezone());
    }

    /**
     * Keeps the timezone of the instance.
     */
    public function toNotNull()
    {
        $class = static::CLASS_REQUIRED;
        return new $class($this->value, $this->value?->getTimezone());
    }

    /**
     * ```
     * d/m/Y H:i:s
     * ```
     */
    public function toDatetimeDMYSlash(): ?string
    {
        return $this->value?->format(DateFormat::datetime_DMY_slash->value);
    }

    /**
     * ```
     * d/m/Y H:i
     * ```
     */
    public function toDatetimeDMYSlashShort(): ?string
    {
        return $this->value?->format(DateFormat::datetime_DMY_slash_short->value);
    }

    /**
     * ```
     * Y-m-d H:i:s
     * ```
     */
    public function toDatetimeYMD(): ?string
    {
        return $this->value?->format(DateFormat::datetime_YMD->value);
    }

    /**
     * ```
     * Y-m-d H:i
     * ```
     */
    public function toDatetimeYMDShort(): ?string
    {
        return $this->value?->format(DateFormat::datetime_YMD_short->value);
    }

    /**
     * Format used by `jsonSerialize()`: `kalion.dates.serialization_format` or, if it is not configured, ISO-8601
     * like Carbon and Eloquent (`2026-03-29T02:30:00.000000Z`, or `2026-03-29T04:30:00.000000+02:00`
     * if `kalion.dates.serialize_to_local_tz` is `true`).
     */
    public static function serializationFormat(): string
    {
        $config = kalion()->config();

        return $config->dateSerializationFormat() ?: ($config->dateSerializeToLocalTz()
            ? DateFormat::datetime_iso_offset->value
            : DateFormat::datetime_eloquent->value);
    }

    /**
     * Timezone used by `jsonSerialize()`: `UTC` if the serialization format contains the Zulu designator (`\Z`),
     * otherwise the local timezone (if `kalion.dates.serialize_to_local_tz` is `true`) or the application timezone,
     * like the custom formats of the Eloquent date casts.
     */
    public static function serializationTimezone(): string
    {
        $default = kalion()->config()->dateSerializeToLocalTz() ? DateHelper::localTimezone() : null;

        return (string)DateHelper::timezoneForFormat(static::serializationFormat(), $default);
    }

    /**
     * Serialization of the date (`serializationFormat()` in `serializationTimezone()`).
     * `from()` always accepts it, so an object can be rebuilt from its own `toArray()`.
     */
    public function jsonSerialize(): mixed
    {
        return $this->value?->tz(static::serializationTimezone())->format(static::serializationFormat());
    }

    /**
     * Storage representation of the date: the first format of `static::$formats` in the timezone that `from()`
     * uses to read it back (the application timezone or `UTC` if the format contains `\Z`).
     * It does not depend on the serialization config.
     */
    public function toDb(): ?string
    {
        $format = static::$formats[0];

        return $this->value?->tz(DateHelper::timezoneForFormat($format))->format($format);
    }

    public function __get($name): mixed
    {
        return $this->value?->{$name};
    }

    /**
     * Forwards the call to the `CarbonImmutable` value (returns `null` if the value is `null`).
     */
    public function __call(string $method, array $arguments): mixed
    {
        return $this->value?->{$method}(...$arguments);
    }
}
