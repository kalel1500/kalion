<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

abstract class AbstractStringVo extends AbstractValueObject
{
    protected const CLASS_REQUIRED = StringVo::class;
    protected const CLASS_NULLABLE = StringNullVo::class;

    /**
     * @var string|null
     */
    public $value;

    public function __construct(?string $value)
    {
        $this->ensureIsValidValue($value);
        $this->value = $value; // $this->clearString($value);
    }

    protected function ensureIsValidValue(?string $value): void
    {
        $this->checkNullable($value);
    }

    /*protected function clearString(?string $value): ?string
    {
        return is_null($value) || empty(trim($value)) ? null : trim($value);
    }*/

    public function contains(string $search): bool
    {
        return str_contains($this->value, $search);
    }
}
