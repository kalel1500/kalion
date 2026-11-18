<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use DateTimeZone;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Date\DateHelper;

/**
 * Date Value Object.
 *
 * - `$value` is the canonical string representation (the one that is validated, serialized and persisted).
 *   Strings without timezone information are interpreted in the application timezone (`date_default_timezone_get()`),
 *   and strings with the Zulu designator (`\Z`) in UTC.
 * - `carbon()` (and therefore `format()` and `toDatetime*()`) is always normalized to the application timezone,
 *   regardless of how the VO was hydrated, unless it is explicitly projected with `tz()`/`timezone()`.
 */
abstract class AbstractDateVo extends AbstractStringVo
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    protected static array     $formats         = [DateFormat::datetime_YMD->value];
    protected ?array           $instanceFormats = null;
    protected ?CarbonImmutable $valueCarbon     = null;

    public function __construct(?string $value, ?array $formats = null)
    {
        // Must be set before parent::__construct(), which runs the format validation
        $this->instanceFormats = $formats;
        parent::__construct($value);
        $this->valueCarbon = is_null($value) ? null : $this->toCarbon($value);
    }

    protected static function resolveFormats(?array $formats = null): array
    {
        return $formats ?? static::$formats;
    }

    protected function resolveInstanceFormats(): array
    {
        return static::resolveFormats($this->instanceFormats);
    }

    /**
     * Parses the (already validated) value with the format it matches, and normalizes it to the application timezone.
     */
    protected function toCarbon(string $value): CarbonImmutable
    {
        $format = DateHelper::firstMatchingFormat($value, $this->resolveInstanceFormats());

        return DateHelper::createFromFormat($format, $value)->tz(date_default_timezone_get());
    }

    public static function from($value, ?array $formats = null): static
    {
        return new static($value, $formats);
    }

    /**
     * Builds the VO from a date instance, preserving the instant: the date is converted to the timezone implied
     * by `$toFormat` (`UTC` for `\Z`, application timezone otherwise) before formatting it.
     */
    public static function fromCarbon(?DateTimeInterface $value, ?string $toFormat = null, ?array $formats = null): static
    {
        if (is_null($value)) {
            return new static(null, $formats);
        }

        $toFormat  = $toFormat ?? static::resolveFormats($formats)[0];
        $formatted = CarbonImmutable::instance($value)
            ->tz(DateHelper::timezoneForFormat($toFormat))
            ->format($toFormat);

        return new static($formatted, $formats);
    }

    /**
     * Builds the VO from any date instance or any string understood by `CarbonImmutable::parse()`
     * (strings without timezone information are interpreted in the application timezone).
     */
    public static function parse($value, ?string $toFormat = null, ?array $formats = null): static
    {
        if (is_null($value) || $value instanceof DateTimeInterface) {
            return static::fromCarbon($value, $toFormat, $formats);
        }

        return static::fromCarbon(CarbonImmutable::parse((string)$value), $toFormat, $formats);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        $formats = $this->resolveInstanceFormats();
        if (! is_null($value) && ! DateHelper::matchesAnyFormat($value, $formats)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', $formats)));
        }
    }

    /**
     * Returns a new instance whose date (`carbon()`, `format()`, `toDatetime*()`) is projected to the given timezone.
     *
     * The canonical `$value` is not modified: it still represents the same instant, so it can be safely
     * serialized or persisted (re-formatting it without timezone information would change the instant).
     */
    public function tz(string|DateTimeZone $timezone): static
    {
        $clone              = clone $this;
        $clone->valueCarbon = $this->valueCarbon?->tz($timezone);
        return $clone;
    }

    /**
     * Alias of `tz()`.
     */
    public function timezone(string|DateTimeZone $timezone): static
    {
        return $this->tz($timezone);
    }

    /**
     * Keeps the instance formats and the projected timezone.
     */
    public function toNull()
    {
        $class = static::CLASS_NULLABLE;
        return $class::from($this->value, $this->instanceFormats)->withCarbonOf($this);
    }

    /**
     * Keeps the instance formats and the projected timezone.
     */
    public function toNotNull()
    {
        $class = static::CLASS_REQUIRED;
        return $class::from($this->value, $this->instanceFormats)->withCarbonOf($this);
    }

    protected function withCarbonOf(self $other): static
    {
        $this->valueCarbon = $other->valueCarbon;
        return $this;
    }

    /**
     * ```
     * d/m/Y H:i:s
     * ```
     */
    public function toDatetimeDMYSlash(): ?string
    {
        return $this->valueCarbon?->format(DateFormat::datetime_DMY_slash->value);
    }

    /**
     * ```
     * d/m/Y H:i
     * ```
     */
    public function toDatetimeDMYSlashShort(): ?string
    {
        return $this->valueCarbon?->format(DateFormat::datetime_DMY_slash_short->value);
    }

    /**
     * ```
     * Y-m-d H:i:s
     * ```
     */
    public function toDatetimeYMD(): ?string
    {
        return $this->valueCarbon?->format(DateFormat::datetime_YMD->value);
    }

    /**
     * ```
     * Y-m-d H:i
     * ```
     */
    public function toDatetimeYMDShort(): ?string
    {
        return $this->valueCarbon?->format(DateFormat::datetime_YMD_short->value);
    }

    public function format(string $format): ?string
    {
        return $this->valueCarbon?->format($format);
    }

    public function carbon(): ?CarbonImmutable
    {
        return $this->valueCarbon;
    }
}
