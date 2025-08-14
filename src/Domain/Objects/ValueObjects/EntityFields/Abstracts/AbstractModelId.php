<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Abstracts;

use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractIntVo;

/**
 * @template T of AbstractModelId
 */
abstract class AbstractModelId extends AbstractIntVo
{
    protected const CLASS_REQUIRED = null;
    protected const CLASS_NULLABLE = null;
    protected const CLASS_MODEL_REQUIRED = ModelId::class;
    protected const CLASS_MODEL_NULLABLE = ModelIdNull::class;

    protected ?int $minimumValueForModelId = null;

    public function __construct(?int $value)
    {
        if (is_null($this->minimumValueForModelId)) {
            $this->minimumValueForModelId = config('kalion.minimum_value_for_model_id');
        }

        parent::__construct($value);
    }

    protected function ensureIsValidValue(?int $value): void
    {
        parent::ensureIsValidValue($value);

        if (!is_null($value) && $value < $this->minimumValueForModelId) {
            throw new InvalidValueException(sprintf('<%s> does not allow the value <%s>.', class_basename(static::class), $value));
        }
    }

    /**
     * @param int|null $id
     * @return T
     */
    public static function from(?int $id)
    {
        $class = is_null($id) ? static::CLASS_MODEL_NULLABLE : static::CLASS_MODEL_REQUIRED;
        return $class::new($id);
    }
}
