<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Contracts\BuildArrayable;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\JsonVo;
use Thehouseofel\Kalion\Domain\Concerns\Relations\ParsesRelationFlags;
use TypeError;

/**
 * @template T of AbstractCollectionBase
 */
abstract class AbstractCollectionBase implements Countable, ArrayAccess, IteratorAggregate, Arrayable, JsonSerializable
{
    use ParsesRelationFlags;

    private static array $typeCache = [];

    /** @var null|string */
    protected const ITEM_TYPE = null;

    protected $items;

    protected bool   $shouldSkipValidation;
    protected ?string $resolvedItemType;

    public function __construct(...$args)
    {
        $firstArg          = $args[0] ?? [];
        $passedSingleArray = count($args) === 1 && is_array($firstArg);
        $items             = match (true) {
            $passedSingleArray && Arr::isAssoc($firstArg) => $firstArg,
            $passedSingleArray                            => array_values($firstArg),
            default                                       => array_values($args),
        };

        $this->resolvedItemType     = static::resolveItemType();
        $this->shouldSkipValidation = is_null($this->resolvedItemType);
        $this->items                = $this->validateItems($items);
    }

    protected static function resolveItemType(): ?string
    {
        $className = static::class;

        if (! isset(self::$typeCache[$className])) {
            if (is_subclass_of($className, AbstractCollectionAny::class)) {
                self::$typeCache[$className] = null;
                return self::$typeCache[$className];
            }

            $ref = new ReflectionClass(static::class); // REFLECTION - cached

            // Opción 1: Atributo #[CollectionOf(SomeClass::class)]
            $attributes = $ref->getAttributes(CollectionOf::class);
            if (! empty($attributes)) {
                self::$typeCache[$className] = $attributes[0]->newInstance()->class;
                return self::$typeCache[$className];
            }

            // Opción 2: Constante ITEM_TYPE en la clase hija
            if (! is_null(static::ITEM_TYPE)) {
                self::$typeCache[$className] = static::ITEM_TYPE;
                return self::$typeCache[$className];
            }

            throw new RequiredDefinitionException(sprintf('Collection %s must define either #[CollectionOf(...)] or const ITEM_TYPE', static::class));
        }

        return self::$typeCache[$className];
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

    private function toAny(array $data): CollectionAny
    {
        return match (true) {
            ! $this->isInstanceOfRelatable() => CollectionAny::fromArray($data),
            default                          => CollectionAny::fromArray($data, $this->with, $this->isFull),
        };
    }

    private function isInstanceOfRelatable(): bool
    {
        return ($this instanceof Relatable);
    }

    private function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof AbstractCollectionBase) {
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
    public function all()
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
    public function collapse()
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
    public function contains($key, $operator = null, $value = null)
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
     * @param AbstractCollectionBase $items
     * @param string|null $field
     * @return static
     */
    public function diff($items, string $field = null)
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
     * @param AbstractCollectionBase|array $items
     * @return static
     */
    public function diffKeys($items)
    {
        $array1 = $this->toArray();
        $array2 = $this->getArrayableItems($items);
        $result = array_diff_key($array1, $array2);
        $diff   = collect($result);
        return $this->toStatic($diff->toArray());
    }

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param  mixed  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null)
    {
        return ! $this->contains(...func_get_args());
    }

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
    public function each(callable $callback)
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
    public function every($key, $operator = null, $value = null)
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
    public function first(?callable $callback = null, $default = null)
    {
        return collect($this->items)->first(...func_get_args());
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
    public function firstWhere($key, $operator = null, $value = null)
    {
        return $this->where(...func_get_args())->first(); // TODO Canals - hacer pruebas y adaptar a Laravel
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
    public function groupBy($groupBy, $preserveKeys = false)
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
    public function implode($value, $glue = null)
    {
        return collect($this->toArray())->implode(...func_get_args());
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
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
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

    /**
     * @return CollectionAny
     */
    public function keys()
    {
        return new CollectionAny(array_keys($this->items));
    }

    /**
     * @return mixed
     */
    public function last(?callable $callback = null, $default = null)
    {
        return collect($this->items)->last(...func_get_args());
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
        return $result->contains(fn($item) => ! $item instanceof AbstractEntity)
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
     * @param string $value
     * @param string|null $key
     * @return CollectionAny
     */
    public function pluck($value, $key = null)
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

            if (property_exists($collectionItem, $pluckField)) {
                return $collectionItem->$pluckField;
            }

            return $collectionItem->toArrayForBuild()[$pluckField];
        };
        $clearItemValue = function ($item) {
            return match (true) {
                $item instanceof Arrayable           => $item->toArray(),
                $item instanceof AbstractValueObject => $item->value(),
                default                              => $item
            };
        };

        $result = [];
        foreach ($this->items as $item) {
            $fieldValue = $getItemValue($item, $value);
            $fieldValue = $clearItemValue($fieldValue);

            if (is_null($key)) {
                $result[] = $fieldValue;
            } else {
                $keyValue          = $getItemValue($item, $key);
                $keyValue          = $clearItemValue($keyValue);
                $result[$keyValue] = $fieldValue;
            }
        }

        if (! $this->isInstanceOfRelatable()) {
            return CollectionAny::fromArray($result);
        }

        $with = is_array($this->with) ? $this->with : [$this->with];
        $relationName = $value;

        $newWith = null;
        $newIsFull = null;
        foreach ($with as $key => $rel) {

            if (is_string($key)) {
                [$key, $isFull] = $this->getInfoFromRelationWithFlag($key);

                if ($key === $relationName) {
                    $newWith = $rel;
                    $newIsFull = $isFull;
                    break;
                }
            } else {
                $arrayRels = explode('.', $rel);
                $firstRel = $arrayRels[0];
                [$firstRel, $isFull] = $this->getInfoFromRelationWithFlag($firstRel);

                if ($firstRel === $relationName) {
                    unset($arrayRels[0]);
                    $newWith = implode('.', $arrayRels);
                    $newIsFull = $isFull;
                    break;
                }
            }
        }
        $newWith = (empty($newWith)) ? null : $newWith;

        return CollectionAny::fromArray($result, $newWith, $newIsFull);
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
    public function push(...$values)
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
    public function put($key, $value)
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
    public function take($limit)
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
                $item instanceof AbstractValueObject              => $item->value(),
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
    public function toJson($options = 0)
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

    /**
     * @param string $key
     * @param array $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false)
    {
        $collResult = collect($this->toArray())->whereNotIn($key, $values, $strict)->values();
        return $this->toStatic($collResult->toArray());
    }

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
