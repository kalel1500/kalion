<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;

abstract class AbstractCollectionVo extends AbstractCollectionBase
{
    /**
     * @return AbstractValueObject|null
     */
    public function first(?callable $callback = null, $default = null)
    {
        return parent::first(...func_get_args());
    }

    public function firstValue()
    {
        return $this->first()?->value();
    }

    public static function fromArray(?array $values, callable $valueModifierCallback = null): static|null
    {
        if (is_null($values)) return null;

        $valueClass = static::resolveItemType();
        $res = [];
        foreach ($values as $key => $value) {
            if ($value instanceof $valueClass) {
                $res[$key] = $value;
            } else {
                if (!is_null($valueModifierCallback)) {
                    $value = $valueModifierCallback($value);
                }
                $res[$key] = new $valueClass($value);
            }
        }
        return new static($res);
    }
}
