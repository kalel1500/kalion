<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Date;

abstract class AbstractDateVo extends AbstractStringVo
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    protected static array $formats    = ['Y-m-d H:i:s'];
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

    public static function parse($value, $formatPosition = 0): static
    {
        $formatted = Date::parse($value)
            ->setTimezone(config('app.timezone'))
            ->format(static::$formats[$formatPosition]);
        return static::from($formatted);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        if (! is_null($value) && ! Date::checkFormats($value, static::$formats, $this->allowZeros)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', static::$formats)));
        }
    }

    public function formatToSpainDatetime(): ?string
    {
        return $this->isNull() ? null : Date::parse($this->value)->format(Date::$datetime_startDay_slash);
    }

    public function carbon(): CarbonImmutable
    {
        return $this->valueCarbon ?? Date::parse($this->value)->setTimezone(config('app.timezone'));
    }
}
