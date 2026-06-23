<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Date\DateHelper;

abstract class AbstractDateVo extends AbstractStringVo
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    protected static array $formats      = [DateFormat::datetime_startYear];
    protected static array $inputFormats = [DateFormat::html_datetime_local, DateFormat::html_datetime_local_withoutSeconds];
    protected ?array       $instanceFormats = null;
    protected              $valueCarbon;

    public function __construct(?string $value, ?array $formats = null)
    {
        $this->instanceFormats = $formats;
        parent::__construct($value);
    }

    protected static function resolveFormats(?array $formats = null): array
    {
        return $formats ?? static::$formats;
    }

    protected function resolveInstanceFormats(): array
    {
        return static::resolveFormats($this->instanceFormats);
    }

    public static function from($value, ?array $formats = null): static
    {
        if ($value instanceof CarbonInterface) {
            return static::fromCarbon($value, null, $formats);
        }

        if (is_null($value)) return new static($value, $formats);

        // Normalize from inputFormats
        $inputFormats = array_map(fn($f) => $f->value, static::$inputFormats);
        if (DateHelper::matchesAnyFormat($value, $inputFormats)) {
            return static::parse($value, null, $formats);
        }

        return new static($value, $formats);
    }

    public static function fromCarbon(CarbonInterface $value, DateFormat $toFormat = null, ?array $formats = null): static
    {
        $effectiveFormats = static::resolveFormats($formats);

        if (is_null($toFormat)) {
            $toFormat = $effectiveFormats[0];
        }

        $formatted = $value->format($toFormat->value);
        $instance = new static($formatted, $formats);
        $instance->valueCarbon = $value->toImmutable();

        return $instance;
    }

    public static function parse($value, DateFormat $toFormat = null, ?array $formats = null): static
    {
        if ($value instanceof CarbonInterface) {
            return static::fromCarbon($value, $toFormat, $formats);
        }

        $effectiveFormats = static::resolveFormats($formats);

        if (is_null($toFormat)) {
            $toFormat = $effectiveFormats[0];
        }
        $formatted = CarbonImmutable::parse($value)->format($toFormat->value);
        return static::from($formatted, $formats);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        $formats = array_map(fn(\BackedEnum $item) => $item->value, $this->resolveInstanceFormats());
        if (! is_null($value) && ! DateHelper::matchesAnyFormat($value, $formats)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', $formats)));
        }
    }

    public function formatToSpainDatetime(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_startDay_slash->value);
    }

    public function formatToSpainDatetimeWithoutSeconds(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_startDay_slash_withoutSeconds->value);
    }

    public function formatDatetime(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_startYear->value);
    }

    public function formatDatetimeWithoutSeconds(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_startYear_withoutSeconds->value);
    }

    public function format(string $format): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format($format);
    }

    public function carbon(): CarbonImmutable
    {
        return $this->valueCarbon ?? CarbonImmutable::parse($this->value);
    }
}
