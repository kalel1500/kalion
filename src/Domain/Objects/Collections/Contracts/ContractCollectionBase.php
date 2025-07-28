<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\SubRelationDataDo;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\ContractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\JsonVo;
use Thehouseofel\Kalion\Domain\Services\Relation;
use TypeError;

/**
 * @template T of ContractCollectionBase
 */
abstract class ContractCollectionBase implements Countable, ArrayAccess, IteratorAggregate, Arrayable, JsonSerializable
{
    /** @var null|string */
    protected const ITEM_TYPE = null;

    protected array $items;

    protected bool   $shouldSkipValidation;
    protected string $resolvedItemType;

    public function __construct(...$args)
    {
        $firstArg          = $args[0] ?? [];
        $passedSingleArray = count($args) === 1 && is_array($firstArg);
        $items             = match (true) {
            $passedSingleArray && Arr::isAssoc($firstArg) => $firstArg,
            $passedSingleArray                            => array_values($firstArg),
            default                                       => array_values($args),
        };

        $this->shouldSkipValidation = $this instanceof ContractCollectionAny;
        $this->resolvedItemType     = static::resolveItemType();
        $this->items                = $this->validateItems($items);
    }

    protected static function resolveItemType(): string
    {
        $ref = new ReflectionClass(static::class);

        // Opción 1: Atributo #[CollectionOf(SomeClass::class)]
        $attributes = $ref->getAttributes(CollectionOf::class);
        if (! empty($attributes)) {
            return $attributes[0]->newInstance()->type;
        }

        // Opción 2: Constante ITEM_TYPE en la clase hija
        if (! is_null(static::ITEM_TYPE)) {
            return static::ITEM_TYPE;
        }

        throw new RequiredDefinitionException(sprintf('Collection %s must define either #[CollectionOf(...)] or const ITEM_TYPE', static::class));
    }

    private function validateItems(array $items): array
    {
        if ($this->shouldSkipValidation) return $items;

        foreach ($items as $item) {
            $this->validateItem($item);
        }

        return $items;
    }

    private function validateItem(mixed $item): void
    {
        if ($this->shouldSkipValidation) return;

        $line = __LINE__ - 4;
        if (! ($item instanceof $this->resolvedItemType)) {
            $givenType = is_object($item) ? get_class($item) : gettype($item);
            throw new TypeError(sprintf(
                '%s::%s(): Argument #1 ($item) must be of type %s, %s given, called in %s on line %s',
                self::class,
                __FUNCTION__,
                $this->resolvedItemType,
                $givenType,
                __FILE__,
                $line,
            ));
        }
    }

    private function toStatic(array $collResult): static
    {
        if ($this->isInstanceOfRelatable()) {
            /** @var Relatable $this */
            return static::fromArray($collResult, $this->with, $this->isFull);
        } else {
            return static::fromArray($collResult);
        }
    }

    private function toAny(array $data, string $pluckField = null): CollectionAny
    {
        $subRelData = (! $this->isInstanceOfRelatable())
            ? SubRelationDataDo::fromArray([null, null])
            : Relation::getNextRelation($this->with, $this->isFull, $pluckField);
        return CollectionAny::fromArray($data, $subRelData->with, $subRelData->isFull);
    }

    private function isInstanceOfRelatable(): bool
    {
        return ($this instanceof Relatable);
    }

    private function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof ContractCollectionBase) {
            return $items->toArray();
        }

        return (array)$items;
    }


    /**
     * @return static
     */
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

    public function offsetGet($offset): mixed
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


//    public function after()
//    {
//        //
//    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

//    public function average()
//    {
//        //
//    }

//    public function avg()
//    {
//        //
//    }

//    public function before()
//    {
//        //
//    }

//    public function chunk()
//    {
//        //
//    }

//    public function chunkWhile()
//    {
//        //
//    }

    /**
     * @return CollectionAny
     */
    public function collapse(): CollectionAny
    {
        $result = collect($this->toArray())->collapse();
        return $this->toAny($result->toArray());
    }

//    public function collapseWithKeys()
//    {
//        //
//    }

//    public function collect()
//    {
//        //
//    }

//    public function combine()
//    {
//        //
//    }

//    public function concat()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        $array = (is_callable($key)) ? $this->items : $this->toArray();
        return collect($array)->contains(...func_get_args());
    }

//    public function containsOneItem()
//    {
//        //
//    }

//    public function containsStrict()
//    {
//        //
//    }

//    public function countBy()
//    {
//        //
//    }

//    public function crossJoin()
//    {
//        //
//    }

//    public function dd()
//    {
//        //
//    }

    /**
     * @param ContractCollectionBase $items
     * @param string|null $field
     * @return static
     */
    public function diff(ContractCollectionBase $items, string $field = null)
    {
        if (! is_null($field)) {
            $diff       = collect();
            $dictionary = $items->pluck($field);
            foreach ($this->toArray() as $item) {
                if (! $dictionary->contains($item[$field])) {
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

        return $this->toStatic($diff->toArray());
    }

//    public function diffAssoc()
//    {
//        //
//    }

//    public function diffAssocUsing()
//    {
//        //
//    }

    /**
     * @param ContractCollectionBase|array $items
     * @return static
     */
    public function diffKeys(ContractCollectionBase|array $items)
    {
        $array1 = $this->toArray();
        $array2 = $this->getArrayableItems($items);
        $result = array_diff_key($array1, $array2);
        $diff   = collect($result);
        return $this->toStatic($diff->toArray());
    }

//    public function doesntContain()
//    {
//        //
//    }

//    public function dot()
//    {
//        //
//    }

//    public function dump()
//    {
//        //
//    }

//    public function duplicates()
//    {
//        //
//    }

//    public function duplicatesStrict()
//    {
//        //
//    }

    /**
     * @param callable $callback
     * @return static
     */
    public function each(callable $callback): static
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }

        return $this;
    }

//    public function eachSpread()
//    {
//        //
//    }

//    public function ensure()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null): bool
    {
        return collect($this->toArray())->every(...func_get_args());
    }

//    public function except()
//    {
//        //
//    }

    /**
     * @param callable|null $callback
     * @return static
     */
    public function filter(callable $callback = null)
    {
        $collResult = collect($this->toArray())->filter($callback)->values();
        return $this->toStatic($collResult->toArray());
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        return collect($this->items)->first();
    }

//    public function firstOrFail()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     * @return mixed
     */
    public function firstWhere($key, $operator = null, $value = null): mixed
    {
        return $this->where(...func_get_args())->first();
    }

    /**
     * @param callable $callback
     * @return CollectionAny
     */
    public function flatMap(callable $callback)
    {
        return $this->map($callback)->collapse();
    }

    /**
     * @param $depth
     * @return static
     */
    public function flatten($depth = INF)
    {
        $collResult = collect($this->toArray())->flatten($depth)->values();
        return $this->toStatic($collResult->toArray());
    }

    /**
     * @return static
     */
    public function flip()
    {
        $result = array_flip($this->toArray());
        $diff   = collect($result);
        return $this->toStatic($diff->toArray());
    }

//    public function forget()
//    {
//        //
//    }

//    public function forPage()
//    {
//        //
//    }

//    public function fromJson()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return value($default);
    }

    /**
     * @param $groupBy
     * @param $preserveKeys
     * @return CollectionAny
     */
    public function groupBy($groupBy, $preserveKeys = false): CollectionAny
    {
        $collResult = collect($this->toArray())->groupBy($groupBy, $preserveKeys);
        if ($collResult->keys()->some('')) throw new RequiredDefinitionException('La key que has indicado no se encuentra en el array del objeto');
        $new = $collResult->map(fn($group) => $this->toStatic($group->toArray()));
        return $this->toAny($new->toArray());
    }

//    public function has()
//    {
//        //
//    }

//    public function hasAny()
//    {
//        //
//    }

    /**
     * @param string $value
     * @return string
     */
    public function implode(string $value): string
    {
        return implode($value, $this->toArray());
    }

//    public function intersect()
//    {
//        //
//    }

//    public function intersectUsing()
//    {
//        //
//    }

//    public function intersectAssoc()
//    {
//        //
//    }

//    public function intersectAssocUsing()
//    {
//        //
//    }

//    public function intersectByKeys()
//    {
//        //
//    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

//    public function join()
//    {
//        //
//    }

//    public function keyBy()
//    {
//        //
//    }

//    public function keys()
//    {
//        //
//    }

    /**
     * @return mixed
     */
    public function last(): mixed
    {
        return collect($this->items)->last();
    }

//    public function lazy()
//    {
//        //
//    }

//    public function macro()
//    {
//        //
//    }

//    public function make()
//    {
//        //
//    }

    /**
     * @param callable $callback
     * @return static|CollectionAny
     */
    public function map(callable $callback)
    {
        $keys        = array_keys($this->items);
        $items       = array_map($callback, $this->items, $keys);
        $result      = collect(array_combine($keys, $items));
        $resultArray = $result->toArray();
        return $result->contains(fn($item) => ! $item instanceof ContractEntity)
            ? $this->toAny($resultArray)
            : $this->toStatic($resultArray);
    }

//    public function mapInto()
//    {
//        //
//    }

//    public function mapSpread()
//    {
//        //
//    }

//    public function mapToGroups()
//    {
//        //
//    }

//    public function mapWithKeys()
//    {
//        //
//    }

//    public function max()
//    {
//        //
//    }

//    public function median()
//    {
//        //
//    }

//    public function merge()
//    {
//        //
//    }

//    public function mergeRecursive()
//    {
//        //
//    }

//    public function min()
//    {
//        //
//    }

//    public function mode()
//    {
//        //
//    }

//    public function multiply()
//    {
//        //
//    }

//    public function nth()
//    {
//        //
//    }

//    public function only()
//    {
//        //
//    }

//    public function pad()
//    {
//        //
//    }

//    public function partition()
//    {
//        //
//    }

//    public function percentage()
//    {
//        //
//    }

//    public function pipe()
//    {
//        //
//    }

//    public function pipeInto()
//    {
//        //
//    }

//    public function pipeThrough()
//    {
//        //
//    }

    /**
     * @param string $field
     * @param string|null $key
     * @return CollectionAny
     */
    public function pluck(string $field, string $key = null): CollectionAny
    {
        $getItemValue   = function ($collectionItem, string $pluckField) {
            /** @var Arrayable|BuildArrayable $collectionItem */

            if (is_array($collectionItem)) {
                return $collectionItem[str_snake($pluckField)];
            }

            if (! is_object($collectionItem)) {
                return null;
            }

            if (method_exists($collectionItem, $pluckField)) {
                return $collectionItem->$pluckField();
            }

            $itemClass = new ReflectionClass($collectionItem);
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
        return $this->toAny($result, $field);
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

//    public function pop()
//    {
//        //
//    }

//    public function prepend()
//    {
//        //
//    }

//    public function pull()
//    {
//        //
//    }

    /**
     * @param ...$values
     * @return static
     */
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->validateItem($value);
            $this->items[] = $value;
        }
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return static
     */
    public function put($key, $value): static
    {
        $this->validateItem($value);
        $this->offsetSet($key, $value);

        return $this;
    }

//    public function random()
//    {
//        //
//    }

//    public function range()
//    {
//        //
//    }
//
//    public function reduce()
//    {
//        //
//    }

//    public function reduceSpread()
//    {
//        //
//    }

//    public function reject()
//    {
//        //
//    }

//    public function replace()
//    {
//        //
//    }

//    public function replaceRecursive()
//    {
//        //
//    }

//    public function reverse()
//    {
//        //
//    }

//    public function search()
//    {
//        //
//    }

    /**
     * @param $keys
     * @return static
     */
    public function select($keys)
    {
        $keys       = is_array($keys) ? $keys : func_get_args();
        $collResult = collect($this->toArray())
            ->map(fn($item) => collect($item)->only($keys)->toArray())
            ->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function shift()
//    {
//        //
//    }

//    public function shuffle()
//    {
//        //
//    }

//    public function skip()
//    {
//        //
//    }

//    public function skipUntil()
//    {
//        //
//    }

//    public function skipWhile()
//    {
//        //
//    }

//    public function slice()
//    {
//        //
//    }

//    public function sliding()
//    {
//        //
//    }

//    public function sole()
//    {
//        //
//    }

//    public function some()
//    {
//        //
//    }

    /**
     * @param $callback
     * @return static
     */
    public function sort($callback = null)
    {
        $collResult = collect($this->toArray())->sort($callback)->values();
        return $this->toStatic($collResult->toArray());
    }

    /**
     * @param $callback
     * @param $options
     * @param $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        $collResult = collect($this->toArray())->sortBy($callback, $options, $descending)->values();
        return $this->toStatic($collResult->toArray());
    }

    /**
     * @param $callback
     * @param $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * @param $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR)
    {
        $collResult = collect($this->toArray())->sortDesc($options)->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function sortKeys()
//    {
//        //
//    }

//    public function sortKeysDesc()
//    {
//        //
//    }
//
//    public function sortKeysUsing()
//    {
//        //
//    }

//    public function splice()
//    {
//        //
//    }

//    public function split()
//    {
//        //
//    }

//    public function splitIn()
//    {
//        //
//    }

//    public function sum()
//    {
//        //
//    }

    /**
     * @param int $limit
     * @return static
     */
    public function take(int $limit)
    {
        $collResult = collect($this->toArray())->take($limit)->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function takeUntil()
//    {
//        //
//    }

//    public function takeWhile()
//    {
//        //
//    }

//    public function tap()
//    {
//        //
//    }

//    public function times()
//    {
//        //
//    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->items as $key => $item) {
            $fromThisClass = (debug_backtrace()[0]['file'] === __FILE__);
            $item = match (true) {
                $item instanceof BuildArrayable && $fromThisClass => $item->toArrayForBuild(),
                $item instanceof Arrayable                        => $item->toArray(),
                $item instanceof ContractValueObject              => $item->value(),
                default                                           => $item,
            };
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
        return object_to_array($this->toArray());
    }

    public function toClearedObject(): object|array
    {
        return array_to_object($this->toArray());
    }

    public function toCollect(): Collection
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

    /**
     * @param $options
     * @return false|string
     */
    public function toJson($options = 0): false|string
    {
        return json_encode($this->toArray(), $options);
    }

    public function toJsonVo(): JsonVo
    {
        return new JsonVo($this->toArray());
    }

//    public function transform()
//    {
//        //
//    }

//    public function undot()
//    {
//        //
//    }

//    public function union()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        $collResult = collect($this->toArray())->unique($key, $strict)->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function uniqueStrict()
//    {
//        //
//    }

//    public function unless()
//    {
//        //
//    }

//    public function unlessEmpty()
//    {
//        //
//    }

//    public function unlessNotEmpty()
//    {
//        //
//    }

//    public function unwrap()
//    {
//        //
//    }

//    public function value()
//    {
//        //
//    }

    /**
     * @return static
     */
    public function values()
    {
        return $this->toStatic(array_values($this->toArray()));
    }

//    public function when()
//    {
//        //
//    }

//    public function whenEmpty()
//    {
//        //
//    }

//    public function whenNotEmpty()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $operator
     * @param $value
     * @return static
     */
    public function where($key, $operator = null, $value = null)
    {
        $collResult = collect($this->toArray())->where(...func_get_args())->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function whereStrict()
//    {
//        //
//    }

//    public function whereBetween()
//    {
//        //
//    }

    /**
     * @param $key
     * @param $values
     * @param $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false)
    {
        $collResult = collect($this->toArray())->whereIn($key, $values, $strict)->values();
        return $this->toStatic($collResult->toArray());
    }

//    public function whereInStrict()
//    {
//        //
//    }

//    public function whereInstanceOf()
//    {
//        //
//    }

//    public function whereNotBetween()
//    {
//        //
//    }

//    public function whereNotIn()
//    {
//        //
//    }

//    public function whereNotInStrict()
//    {
//        //
//    }

    /**
     * @param $key
     * @return static
     */
    public function whereNotNull($key = null)
    {
        return $this->where($key, '!==', null);
    }

//    public function whereNull()
//    {
//        //
//    }

//    public function wrap()
//    {
//        //
//    }

//    public function zip()
//    {
//        //
//    }
}
