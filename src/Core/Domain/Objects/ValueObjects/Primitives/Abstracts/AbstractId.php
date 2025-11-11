<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractIntVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;

abstract class AbstractId extends AbstractIntVo
{
    protected const CLASS_REQUIRED = IdVo::class;
    protected const CLASS_NULLABLE = IdNullVo::class;

    protected ?int $minimumValueForId = null;

    public function __construct(?int $value)
    {
        if (is_null($this->minimumValueForId)) {
            $this->minimumValueForId = config('kalion.minimum_value_for_id');
        }

        parent::__construct($value);
    }

    protected function ensureIsValidValue(?int $value): void
    {
        parent::ensureIsValidValue($value);

        if (! is_null($value) && $value < $this->minimumValueForId) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s>.', class_basename(static::class), $value));
        }
    }

    /**
     * @param int|null $id
     * @return ($id is null ? null : IdVo|IdNullVo)
     */
    public static function resolve(?int $id)
    {
        $class = is_null($id) ? static::CLASS_NULLABLE : static::CLASS_REQUIRED;
        return $class::from($id);
    }
}
