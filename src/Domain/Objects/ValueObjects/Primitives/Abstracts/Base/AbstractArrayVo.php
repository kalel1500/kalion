<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\ArrayVo;

abstract class AbstractArrayVo extends AbstractValueObject implements ArrayAccess, IteratorAggregate
{
    protected const CLASS_REQUIRED = ArrayVo::class;
    protected const CLASS_NULLABLE = ArrayNullVo::class;

    public function __construct(?array $value)
    {
        $this->value = $value;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->value);
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->value);
    }

    public function offsetGet($offset)
    {
        return $this->value[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }

    public function offsetUnsetLike(string $key)
    {
//        $pattern = "/^$key/";
//        $keysToUnset = array_values(preg_grep($pattern, array_keys($this->value)));
        $keysToUnset = array_keys(array_filter($this->value, fn($item) => str_contains($item, $key), ARRAY_FILTER_USE_KEY));
        foreach ($keysToUnset as $key) {
            unset($this->value[$key]);
        }
    }

    public function value(): ?array
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return empty($this->value);
    }

    public function count(): ?int
    {
        return ($this->isNull()) ? null : count($this->value);
    }

    public function has(string|array $key): bool
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value) {
            if (! $this->offsetExists($value)) {
                return false;
            }
        }
        return true;
    }

    public function get(string $key, $default = null)
    {
        if ($this->offsetExists($key)) {
            return $this->value[$key];
        }

        return $default;
    }

    public function put(mixed $key, mixed $value): static
    {
        $this->offsetSet($key, $value);

        return $this;
    }

    public function forget(string|array $keys): static
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnset($key);
        }

        return $this;
    }

    public function forgetLike(string|array $keys): static
    {
        foreach ((array)$keys as $key) {
            $this->offsetUnsetLike($key);
        }

        return $this;
    }

    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->value[] = $value;
        }

        return $this;
    }

    public function toJson(): ?string
    {
        return ($this->isEmpty()) ? null : json_encode($this->value);
    }

}
