<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Infrastructure\Services\Date;

abstract class AbstractDateVo extends AbstractStringVo
{
    protected const CLASS_REQUIRED = DateVo::class;
    protected const CLASS_NULLABLE = DateNullVo::class;

    protected bool  $allowZeros = false;
    protected array $formats    = ['Y-m-d H:i:s'];
    protected       $valueCarbon;

    public function __construct(?string $value, ?array $formats = null)
    {
        $this->formats = is_null($formats) ? $this->formats : $formats;
        parent::__construct($value);
    }

    public static function from($value, ?array $formats = null): static
    {
        return new static($value, $formats);
    }

    public static function parse($value): static
    {
        $formatted = Date::parse($value)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
        return static::from($formatted);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        if (! is_null($value) && ! Date::checkFormats($value, $this->formats, $this->allowZeros)) {
            throw new InvalidValueException(sprintf('<%s> does not allow this format value <%s>. Needle formats: <%s>', class_basename(static::class), $value, implode(', ', $this->formats)));
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
