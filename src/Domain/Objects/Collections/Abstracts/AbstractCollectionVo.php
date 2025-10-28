<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

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

    /**
     * @template T of array|null
     * @param T $data
     * @param callable|null $valueModifierCallback
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data, callable $valueModifierCallback = null): ?static
    {
        if (is_null($data)) return null;

        $valueClass = static::resolveItemType();
        $res        = [];
        foreach ($data as $key => $value) {
            if ($value instanceof $valueClass) {
                $res[$key] = $value;
            } else {
                if (! is_null($valueModifierCallback)) {
                    $value = $valueModifierCallback($value);
                }
                $res[$key] = new $valueClass($value);
            }
        }
        return new static($res);
    }
}
