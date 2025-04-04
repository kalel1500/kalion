<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelDate;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelDateNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\DateVo;
use Thehouseofel\Kalion\Infrastructure\Services\Date;

abstract class ContractDateVo extends ContractStringVo
{
    protected const CLASS_REQUIRED       = DateVo::class;
    protected const CLASS_NULLABLE       = DateNullVo::class;
    protected const CLASS_MODEL_REQUIRED = ModelDate::class;
    protected const CLASS_MODEL_NULLABLE = ModelDateNull::class;

    protected bool  $allowZeros = false;
    protected array $formats    = ['Y-m-d H:i:s'];
    protected       $valueCarbon;

    public function __construct(?string $value, ?array $formats = null)
    {
        $this->formats = is_null($formats) ? $this->formats : $formats;
        parent::__construct($value);
    }

    public static function new($value, ?array $formats = null): static
    {
        return new static($value, $formats);
    }

    public static function from($value): static
    {
        $formatted = Date::parse($value)
            ->setTimezone(config('app.timezone'))
            ->format('Y-m-d H:i:s');
        return static::new($formatted);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        parent::ensureIsValidValue($value);

        if (!is_null($value) && !Date::checkFormats($value, $this->formats, $this->allowZeros)) {
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
