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

    protected static array    $formats         = [DateFormat::datetime_YMD->value];
    protected static array    $inputFormats    = [DateFormat::html_datetime->value, DateFormat::html_datetime_short->value];
    protected ?array          $instanceFormats = null;
    protected CarbonImmutable $valueCarbon;

    public function __construct(?string $value, ?array $formats = null)
    {
        $this->instanceFormats = $formats;
        $this->valueCarbon     = CarbonImmutable::parse($this->value);
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

        // Normalize from inputFormats
        if (! is_null($value) && DateHelper::matchesAnyFormat($value, static::$inputFormats)) {
            return static::parse($value, null, $formats);
        }

        return new static($value, $formats);
    }

    public static function fromCarbon(?CarbonInterface $value, string $toFormat = null, ?array $formats = null): static
    {
        $toFormat  = $toFormat ?? static::resolveFormats($formats)[0];
        $formatted = $value?->format($toFormat);
        return new static($formatted, $formats);
    }

    public static function parse($value, string $toFormat = null, ?array $formats = null): static
    {
        if ($value instanceof CarbonInterface) {
            return static::fromCarbon($value, $toFormat, $formats);
        }

        $toFormat  = $toFormat ?? static::resolveFormats($formats)[0];
        $formatted = CarbonImmutable::parse($value)->format($toFormat);
        return new static($formatted, $formats);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        $formats = $this->resolveInstanceFormats();
        if (! is_null($value) && ! DateHelper::matchesAnyFormat($value, $formats)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', $formats)));
        }
    }

    public function toDatetimeDMYSlash(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_DMY_slash->value);
    }

    public function toDatetimeDMYSlashShort(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_DMY_slash_short->value);
    }

    public function toDatetimeYMD(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_YMD->value);
    }

    public function toDatetimeYMDShort(): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format(DateFormat::datetime_YMD_short->value);
    }

    public function format(string $format): ?string
    {
        return $this->isNull() ? null : CarbonImmutable::parse($this->value)->format($format);
    }

    public function carbon(): CarbonImmutable
    {
        return $this->valueCarbon;
    }
}
