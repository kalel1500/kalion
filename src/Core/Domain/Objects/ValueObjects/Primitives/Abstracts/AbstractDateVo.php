<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\DateFormat;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Date;

abstract class AbstractDateVo extends AbstractStringVo
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    protected static array $formats    = [DateFormat::datetime_startYear]; // Debe ser estática para poder usarla en el metodo estático parse
    protected bool         $allowZeros = false;
    protected              $valueCarbon;

    public function __construct(?string $value, ?array $formats = null)
    {
        static::$formats = is_null($formats) ? static::$formats : $formats;
        parent::__construct($value);
    }

    public static function from($value, ?array $formats = null): static
    {
        return new static($value, $formats);
    }

    public static function parse($value, DateFormat $toFormat = null, ?array $formats = null): static
    {
        if (is_null($toFormat)) {
            $toFormat = static::$formats[0];
        }
        $formatted = Date::parse($value)->format($toFormat->value);
        return static::from($formatted, $formats);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        $formats = array_map(fn(\BackedEnum $item) => $item->value, static::$formats);
        if (! is_null($value) && ! Date::checkFormats($value, $formats, $this->allowZeros)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', $formats)));
        }
    }

    public function formatToSpainDatetime(): ?string
    {
        return $this->isNull() ? null : Date::parse($this->value)->format(DateFormat::datetime_startDay_slash->value);
    }

    public function carbon(): CarbonImmutable
    {
        return $this->valueCarbon ?? Date::parse($this->value);
    }
}
