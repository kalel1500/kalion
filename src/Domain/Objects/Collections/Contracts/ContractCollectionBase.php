<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Exceptions\NeverCalledException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\SubRelationDataDo;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\JsonVo;

/**
 * @template T of ContractCollectionBase
 */
abstract class ContractCollectionBase implements Countable, ArrayAccess, IteratorAggregate, Arrayable, JsonSerializable
{
    /** @var null|bool */
    protected const IS_ENTITY = null;
    /** @var null|string */
    protected const ENTITY = null;
    /** @var null|string */
    protected const VALUE_CLASS = null;
    /** @var null|string */
    protected const VALUE_CLASS_REQ = null;
    /** @var null|string */
    protected const VALUE_CLASS_NULL = null;

    protected array $items;
    protected bool  $nullable = true;

    /**
     * @param array $collResult
     * @return T
     */
    private function toOriginal(array $collResult)
    {
        if ($this->isInstanceOfRelatable()) return static::fromArray($collResult, $this->with, $this->isFull);
        if ($this instanceof ContractCollectionVo) return static::fromArray($collResult, $this->nullable);
        if ($this instanceof ContractCollectionDo) return static::fromArray($collResult);
        throw new NeverCalledException('La instancia de la colección no extiende de ninguna entidad valida.');
    }

    private function toBase(array $data, string $pluckField = null): CollectionAny
    {
        $subRelData = (!$this->isInstanceOfRelatable())
            ? SubRelationDataDo::fromArray([null, null])
            : get_sub_with($this->with, $this->isFull, $pluckField);
        return CollectionAny::fromArray($data, $subRelData->with, $subRelData->isFull);
    }

    private function encodeAndDecode(array $array, bool $assoc)
    {
        $res = json_encode($array);
        return json_decode($res, $assoc);
    }

    private function isInstanceOfRelatable(): bool
    {
        return ($this instanceof Relatable);
    }

    protected function ensureIsValid($value): void
    {
        $valueClass = ($this->nullable) ? static::VALUE_CLASS_NULL : static::VALUE_CLASS_REQ;
        $valueClass = is_null($valueClass) ? static::VALUE_CLASS : $valueClass;
        $class      = ($this->isEntity()) ? static::ENTITY : $valueClass;

        if (!$class) {
            throw new RequiredDefinitionException(sprintf('The const <%s> must be declared in <%s>.', 'VALUE_CLASS', class_basename(static::class)));
        }
        if (!(is_string($class) && $class === 'any') && !($value instanceof $class)) {
            $provided = is_object($value) ? get_class($value) : $value;
            throw new InvalidValueException(sprintf('The value of <%s> must be an instance of <%s>. Provided <%s>', class_basename(static::class), $class, $provided));
        }
    }

    protected function isEntity(): ?bool
    {
//        return (method_exists($this->values[0], 'toArray'));
        return static::IS_ENTITY;
    }

    public static function empty(): static
    {
        return new static();
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function countInt(): IntVo
    {
        return new IntVo(count($this->items));
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function all(): array
    {
        return $this->items;
    }

    public function first(): mixed
    {
        return coll_first($this->items);
    }

    public function last(): mixed
    {
        return coll_last($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }

    public function push($value)
    {
        $this->ensureIsValid($value);
        return $this->items[] = $value;
    }

    private static function getItemToArray($item)
    {
        $fromThisClass = (debug_backtrace()[0]['file'] === __FILE__);

        return match (true) {
            $item instanceof BuildArrayable && $fromThisClass => $item->toArrayForBuild(),
            $item instanceof Arrayable                        => $item->toArray(),
            $item instanceof ContractValueObject              => $item->value(),
            default                                           => $item,
        };
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->items as $key => $item) {
            $item         = self::getItemToArray($item);
            $result[$key] = $item;
        }
        return $result;
    }

    public function toArrayDynamic($toArrayMethod, ...$params): array
    {
        return array_map(fn($item) => $item->$toArrayMethod(...$params), $this->items);
    }

    public function toClearedArray(): array
    {
        return $this->encodeAndDecode($this->toArray(), true);
    }

    public function toClearedObject(): object|array
    {
        return $this->encodeAndDecode($this->toArray(), false);
    }

    public function toCollect()
    {
        return collect($this->toArray());
    }

    /**
     * @param class-string<T> $collectionClass
     * @return T
     */
    public function toCollection(string $collectionClass)
    {
        if (is_subclass_of($collectionClass, Relatable::class)) {
            return $collectionClass::fromArray($this->toArray(), $this->with, $this->isFull);
        }
        return $collectionClass::fromArray($this->toArray());
    }

    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toJsonVo(): JsonVo
    {
        return new JsonVo($this->toArray());
    }

    public function pluck(string $field, string $key = null): CollectionAny
    {
        $getItemValue   = function ($collectionItem, string $pluckField) {
            /** @var Arrayable|BuildArrayable $collectionItem */

            if (is_array($collectionItem)) {
                return $collectionItem[str_snake($pluckField)];
            }

            if (!is_object($collectionItem)) {
                return null;
            }

            if (method_exists($collectionItem, $pluckField)) {
                return $collectionItem->$pluckField();
            }

            $itemClass = new \ReflectionClass($collectionItem);
            if ($itemClass->hasProperty($pluckField) && $itemClass->getProperty($pluckField)->isPublic()) {
                return $collectionItem->$pluckField;
            }

            return $collectionItem->toArrayForBuild()[$pluckField];
        };
        $clearItemValue = function ($item) {
            return match (true) {
                $item instanceof Arrayable           => $item->toArray(),
                $item instanceof ContractValueObject => $item->value(),
                default                              => $item
            };
        };

        $result = [];
        foreach ($this->items as $item) {
            $fieldValue = $getItemValue($item, $field);
            $fieldValue = $clearItemValue($fieldValue);

            if (is_null($key)) {
                $result[] = $fieldValue;
            } else {
                $keyValue          = $getItemValue($item, $key);
                $keyValue          = $clearItemValue($keyValue);
                $result[$keyValue] = $fieldValue;
            }
        }

//        return (new CollectionAnyVo($result))->whereNotNull();
        return $this->toBase($result, $field);
    }

    /**
     * @param string $field
     * @param class-string<T> $to
     * @return T
     */
    public function pluckTo(string $field, string $to)
    {
        return $this->pluck($field)->toCollection($to);
    }

    public function collapse(): CollectionAny
    {
        $results = [];
        foreach ($this->items as $item) {
            if ($item instanceof Arrayable) {
                $item = $item->toArray();
            }
            if (is_null($item)) {
                continue;
            }
            /*if (!is_array($item)) {
                continue;
            }*/
            $results[] = (!is_array($item) || !array_has_only_arrays($item)) ? [$item] : $item;
        }

        return $this->toBase(array_merge([], ...$results));
    }

    /**
     * @param $callback
     * @param $options
     * @param $descending
     * @return T
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        $collResult = coll_sort_by($this->toArray(), $callback, $options, $descending)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    public function where($key, $operator = null, $value = null)
    {
        $collResult = coll_where($this->toArray(), ...func_get_args())->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function whereIn($key, $values, $strict = false)
    {
        $collResult = coll_where_in($this->toArray(), $key, $values, $strict)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function contains($key, $operator = null, $value = null): bool
    {
        $array = (is_callable($key)) ? $this->items : $this->toArray();
        return coll_contains($array, ...func_get_args());
    }

    public function whereNotNull($key = null)
    {
        return $this->where($key, '!==', null);
    }

    public function values()
    {
        return $this->toOriginal(array_values($this->toArray()));
    }

    public function unique($key = null, $strict = false)
    {
        $collResult = coll_unique($this->toArray(), $key, $strict)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function filter(callable $callback = null)
    {
        $collResult = coll_filter($this->toArray(), $callback)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function implode(string $value): string
    {
        return implode($value, $this->toArray());
    }

    public function sort($callback = null)
    {
        $collResult = coll_sort($this->toArray(), $callback)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function sortDesc($options = SORT_REGULAR)
    {
        $collResult = coll_sort_desc($this->toArray(), $options)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function groupBy($groupBy, $preserveKeys = false): CollectionAny
    {
        $collResult = coll_group_by($this->toArray(), $groupBy, $preserveKeys);
        if ($collResult->keys()->some('')) throw new RequiredDefinitionException('La key que has indicado no se encuentra en el array del objeto');
        $new = $collResult->map(function ($group) {
            return $this->toOriginal($group->toArray());
            /*$group->map(function ($item) {
                dd($item);
                return $this->toOriginal($item->toArray());
            });*/
        });
//        dd($new);
        return $this->toBase($new->toArray());
    }

    public function select($keys)
    {
        $keys       = is_array($keys) ? $keys : func_get_args();
        $collResult = coll_select($this->toArray(), $keys)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function diff(ContractCollectionBase $items, string $field = null)
    {
        if (!is_null($field)) {
            $diff       = collect();
            $dictionary = $items->pluck($field);
            foreach ($this->toArray() as $item) {
                if (!$dictionary->contains($item[$field])) {
                    $diff->add($item);
                }
            }
        } else {
            $array1 = array_map('json_encode', $this->toArray());
            $array2 = array_map('json_encode', $items->toArray());

            $result = array_diff($array1, $array2);
            $result = array_map(fn($item) => json_decode($item, true), $result);

            $diff = collect($result)->values();
        }

        return $this->toOriginal($diff->toArray());
    }

    /**
     * @param ContractCollectionBase|array $items
     * @return T
     */
    public function diffKeys(ContractCollectionBase|array $items)
    {
        $array1 = $this->toArray();
        $array2 = $this->getArrayableItems($items);
        $result = array_diff_key($array1, $array2);
        $diff   = collect($result);
        return $this->toOriginal($diff->toArray());
    }

    public function flip()
    {
        $result = array_flip($this->toArray());
        $diff   = collect($result);
        return $this->toOriginal($diff->toArray());
    }

    public function map(callable $callback)
    {
        $keys        = array_keys($this->items);
        $items       = array_map($callback, $this->items, $keys);
        $result      = collect(array_combine($keys, $items));
        $resultArray = $result->toArray();
        return $result->contains(function ($item) {
            return !$item instanceof ContractEntity;
        }) ? $this->toBase($resultArray) : $this->toOriginal($resultArray);
    }

    public function flatten($depth = INF)
    {
        $collResult = coll_flatten($this->toArray(), $depth)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function take(int $limit)
    {
        $collResult = coll_take($this->toArray(), $limit)->values();
        return $this->toOriginal($collResult->toArray());
    }

    public function firstWhere($key, $operator = null, $value = null): mixed
    {
        return $this->where(...func_get_args())->first();
    }

    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

    protected function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof ContractCollectionBase) {
            return $items->toArray();
        }

        return (array)$items;
    }
}
