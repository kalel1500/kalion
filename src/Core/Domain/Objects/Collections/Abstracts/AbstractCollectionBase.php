<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use IteratorAggregate;
use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Core\Domain\Concerns\Relations\ParsesRelationFlags;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Contracts\Relatable;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Contracts\MakeArrayable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\JsonVo;
use Thehouseofel\Kalion\Core\Domain\Support\Internal\Serialization;
use TypeError;

/**
 * @template TItem Tipo de los items de la colección (entidad, DTO, Value Object... o `mixed` en `CollectionAny`).
 */
abstract class AbstractCollectionBase implements Countable, ArrayAccess, IteratorAggregate, ArrayConvertible, JsonSerializable
{
    use ParsesRelationFlags;

    private static array $typeCache = [];

    /** @var null|string */
    protected const ITEM_TYPE = null;

    protected $items;

    protected bool    $shouldSkipValidation;
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
        $this->items                = $this->assertItemsTypeResolved($items);
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

    private function assertItemsTypeResolved(array $items): array
    {
        if ($this->shouldSkipValidation) return $items;

        foreach ($items as $item) {
            $this->assertItemTypeResolved($item);
        }

        return $items;
    }

    private function doAssertItemType(mixed $item, ?string $expectedType): void
    {
        if ($this->shouldSkipValidation) return;

        if (! ($item instanceof $expectedType)) {
            $traceData = debug_backtrace(limit: 4)[3];
            throw new TypeError(sprintf(
                '%s::%s(): Argument #1 ($item) must be of type %s, %s given, called in %s on line %s',
                $traceData['class'],
                $traceData['function'],
                $expectedType,
                get_debug_type($item),
                $traceData['file'],
                $traceData['line'],
            ));
        }
    }

    protected function assertItemTypeResolved(mixed $item): void
    {
        $this->doAssertItemType($item, $this->resolvedItemType);
    }

    protected function assertItemType(mixed $item, string $expectedType): void
    {
        $this->doAssertItemType($item, $expectedType);
    }

    private function toAny(array $data): CollectionAny
    {
        return match (true) {
            ! $this->isInstanceOfRelatable() => CollectionAny::fromArray($data),
            default                          => CollectionAny::fromArray($data, $this->with, $this->isFull),
        };
    }

    /**
     * Crea una nueva instancia de la colección a partir de items ya construidos (entidades, DTOs, VOs...),
     * sin pasar por `fromArray()`: los items no se serializan ni se reconstruyen, por lo que se conserva
     * su identidad (mismas instancias, valores `computed` cacheados, estado interno...).
     *
     * Se usa `clone` para no depender del constructor (variádico o redefinido en las clases hijas) y para
     * conservar el estado de la colección: `with` e `isFull` en las colecciones `Relatable`, y el tipo
     * de item ya resuelto. Los items no se revalidan porque proceden de esta misma colección.
     *
     * Por defecto la información de paginación no se conserva (igual que al reconstruir con `fromArray()`),
     * ya que deja de corresponderse con los items resultantes. Los métodos que solo reordenan o reindexan
     * la página actual (`values`, `reverse`, `sort*`) pasan `$keepPagination = true` para conservarla.
     *
     * @param array<array-key, TItem> $items
     * @param bool $keepPagination Conservar la paginación (solo si los items son los mismos, reordenados o reindexados)
     * @return static
     */
    private function fromItems(array $items, bool $keepPagination = false): static
    {
        $new        = clone $this;
        $new->items = $items;

        if (! $keepPagination) {
            $new->clearPagination();
        }

        return $new;
    }

    /**
     * Hook para descartar la información de paginación de una colección derivada. Por defecto no hace nada;
     * `AbstractCollectionEntity` lo sobrescribe, que es la única que guarda datos de paginación.
     */
    protected function clearPagination(): void
    {
        //
    }

    /**
     * Crea una nueva instancia con los items originales correspondientes a las claves de `$result`,
     * respetando su orden.
     *
     * Permite calcular un resultado sobre la representación en array (`toArray()`) y devolver después
     * las instancias originales en lugar de reconstruirlas.
     * Solo es válido si la operación de Laravel usada para obtener `$result` conserva las claves.
     *
     * @param array<array-key, mixed> $result
     * @param bool $keepPagination Ver `fromItems()`
     * @return static
     */
    private function fromResultKeys(array $result, bool $keepPagination = false): static
    {
        $items = [];
        foreach (array_keys($result) as $key) {
            $items[$key] = $this->items[$key];
        }

        return $this->fromItems($items, $keepPagination);
    }

    /**
     * Mismo criterio que Laravel (`useAsCallable`): los strings no se tratan como callables,
     * para que nombres de campo como `'count'` o `'date'` no se confundan con funciones de PHP.
     */
    private static function useAsCallable(mixed $value): bool
    {
        return ! is_string($value) && is_callable($value);
    }

    private function isInstanceOfRelatable(): bool
    {
        return ($this instanceof Relatable);
    }

    /**
     * Devuelve los items de `$items` como array sin serializarlos: en las colecciones del paquete y en las
     * de Laravel, los items originales (`all()`). El resto de valores se convierten con el mismo criterio
     * que Laravel (`null` => `[]`, escalares => `[$valor]`, `Arrayable`, `Traversable`...).
     */
    private function getArrayableItems($items): array
    {
        return $items instanceof AbstractCollectionBase
            ? $items->all()
            : (new Collection($items))->all();
    }

    /**
     * Devuelve `$values` en el mismo formato con el que se comparan los items (`toArray()`) si es una
     * colección del paquete (por ejemplo, `whereIn('id', $collectionIds)`). Si no, lo devuelve tal cual.
     */
    private static function comparableValues(mixed $values): mixed
    {
        return $values instanceof AbstractCollectionBase ? $values->toArray() : $values;
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

    /**
     * Valida el tipo del item igual que `push()`/`put()`. Con `$offset` `null` (`$coll[] = $item`)
     * se añade al final, igual que en Laravel.
     */
    public function offsetSet($offset, $value): void
    {
        $this->assertItemTypeResolved($value);

        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
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
     * @return array<array-key, TItem>
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
     * Une en una sola lista los items que sean arrays o colecciones (del paquete o de Laravel), igual que
     * en Laravel. Se trabaja sobre los items originales: los objetos que contengan (entidades, DTO, Value
     * Objects...) se conservan tal cual. Los items que no sean arrays ni colecciones se ignoran.
     *
     * @return CollectionAny
     */
    public function collapse(): CollectionAny
    {
        $results = [];
        foreach ($this->items as $values) {
            if (is_array($values)) {
                $results[] = $values;
            } elseif ($values instanceof AbstractCollectionBase || $values instanceof Collection) {
                $results[] = $values->all();
            }
        }

        return $this->toAny(array_merge([], ...$results));
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
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo o un valor, se busca sobre la representación en array (props y valores de los
     * Value Objects). Los strings nunca se tratan como callables (`'count'`, `'date'`... son campos).
     *
     * @param (callable(TItem, int|string): bool)|string|mixed $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null): bool
    {
        $array = self::useAsCallable($key) ? $this->items : $this->toArray();
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
     * Devuelve los items que no están en `$items`, comparando sobre la representación en array (`toArray()`)
     * de ambas colecciones (por el campo `$field`, que admite notación con puntos y `camelCase` igual que
     * `pluck()`, o por el item completo). Se devuelven las instancias originales conservando las claves,
     * igual que en Laravel.
     *
     * @param AbstractCollectionBase $items
     * @param string|null $field
     * @return static
     */
    public function diff($items, ?string $field = null): static
    {
        if (! is_null($field)) {
            $result     = [];
            $array      = $this->toArray();
            $path       = $this->normalizeDotPath($array, $field);
            $dictionary = $items->pluck($field);
            foreach ($array as $key => $item) {
                if (! $dictionary->contains(data_get($item, $path))) {
                    $result[$key] = $item;
                }
            }
        } else {
            $array1 = array_map('json_encode', $this->toArray());
            $array2 = array_map('json_encode', $items->toArray());

            // array_diff conserva las claves de $array1
            $result = array_diff($array1, $array2);
        }

        return $this->fromResultKeys($result);
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
     * Devuelve los items cuyas claves no están en `$items`, conservando las claves y las instancias originales.
     * Solo se comparan claves, por lo que no se serializa ningún item.
     *
     * @param AbstractCollectionBase|\Illuminate\Support\Enumerable|iterable $items
     * @return static
     */
    public function diffKeys($items): static
    {
        return $this->fromItems(array_diff_key($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
     * @return bool
     */
    public function doesntContain($key, $operator = null, $value = null): bool
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
     * @param callable(TItem, int|string): mixed $callback
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
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo, se compara sobre la representación en array (props y valores de los Value Objects).
     *
     * @param (callable(TItem, int|string): bool)|string $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null): bool
    {
        if (self::useAsCallable($key)) {
            return collect($this->items)->every($key);
        }

        return collect($this->toArray())->every(...func_get_args());
    }

//    public function except()
//    {
//        //
//    }

    /**
     * Filtra la colección conservando las claves.
     *
     * El callback recibe los items originales de la colección (las mismas instancias, no arrays) y su clave.
     * Si no se pasa callback, se eliminan los items que evalúen a `false`.
     *
     * @param (callable(TItem, int|string): bool)|null $callback
     * @return static
     */
    public function filter(?callable $callback = null): static
    {
        return $this->fromItems(collect($this->items)->filter($callback)->all());
    }

    /**
     * @param (callable(TItem, int|string): bool)|null $callback
     * @param mixed $default
     * @return TItem|mixed
     */
    public function first(?callable $callback = null, $default = null): mixed
    {
        return collect($this->items)->first(...func_get_args());
    }

//    public function firstOrFail()
//    {
//        //
//    }

    /**
     * Equivale a `where(...)->first()`, igual que en Laravel: admite un campo (con o sin operador) o un
     * callable, y devuelve el item original o `null`.
     *
     * @param (callable(TItem, int|string): bool)|string $key
     * @param $operator
     * @param $value
     * @return TItem|null
     */
    public function firstWhere($key, $operator = null, $value = null): mixed
    {
        return $this->where(...func_get_args())->first();
    }

    /**
     * @param callable(TItem, int|string): mixed $callback
     * @return CollectionAny
     */
    public function flatMap(callable $callback): CollectionAny
    {
        return $this->map($callback)->collapse();
    }

    /**
     * Aplana la representación en array de la colección (`toArray()`: props, valores de los Value Objects
     * y, en los DTO, también los campos computed).
     *
     * Genera items nuevos (escalares), que no son del tipo de la colección, por lo que siempre
     * devuelve una `CollectionAny`.
     *
     * @param $depth
     * @return CollectionAny
     */
    public function flatten($depth = INF): CollectionAny
    {
        $collResult = collect($this->toArray())->flatten($depth);
        return $this->toAny($collResult->all());
    }

    /**
     * Intercambia claves y valores de la representación en array (`toArray()`) de la colección. Requiere
     * que los valores sean escalares (por ejemplo, colecciones de Value Objects).
     *
     * Los nuevos valores son las claves antiguas, que no son del tipo de la colección, por lo que siempre
     * devuelve una `CollectionAny`.
     *
     * @return CollectionAny
     */
    public function flip(): CollectionAny
    {
        return $this->toAny(array_flip($this->toArray()));
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
     * @return TItem|mixed
     */
    public function get($key, $default = null): mixed
    {
        if (array_key_exists($key, $this->items)) {
            return $this->items[$key];
        }

        return value($default);
    }

    /**
     * Si `$groupBy` (o el primer nivel, si es un array) es un callable, recibe los items originales de la
     * colección (no arrays) y su clave. Si es un campo, se agrupa sobre la representación en array.
     * En ambos casos cada grupo contiene las mismas instancias originales.
     *
     * @param (callable(TItem, int|string): array-key)|string|array $groupBy
     * @param $preserveKeys
     * @return CollectionAny
     */
    public function groupBy($groupBy, $preserveKeys = false): CollectionAny
    {
        // --- 1. Separar el primer nivel del resto (igual que Laravel) ---
        $nextGroups = [];
        if (! self::useAsCallable($groupBy) && is_array($groupBy)) {
            $nextGroups = $groupBy;
            $groupBy    = array_shift($nextGroups);
        }

        // --- 2 y 3. Agrupar este nivel y convertir cada grupo en tu colección tipada ---
        if (self::useAsCallable($groupBy)) {
            // Con callable: se agrupan los items originales y cada grupo conserva las mismas instancias
            $mapped = collect($this->items)
                ->groupBy($groupBy, $preserveKeys)
                ->map(fn(Collection $group) => $this->fromItems($group->all()));
        } else {
            // Por campo: se agrupa sobre la representación en array conservando siempre las claves, para
            // poder recuperar los items originales con fromResultKeys. Después se reindexa cada grupo
            // si no se pidió $preserveKeys (mismo orden que Laravel, que añade los items según los recorre).
            $mapped = collect($this->toArray())
                ->groupBy($groupBy, true)
                ->map(function (Collection $group) use ($preserveKeys) {
                    $group = $this->fromResultKeys($group->all());
                    return $preserveKeys ? $group : $group->values();
                });
        }

        // --- 4. Envolver todo en una CollectionAny ---
        // Se usa all() y no toArray(): el toArray() de Laravel convertiría en array cualquier item Arrayable
        $result = $this->toAny($mapped->all());

        // --- 5. Si quedan niveles, aplicar groupBy recursivamente en cada grupo ---
        if (!empty($nextGroups)) {
            // $result es una CollectionAny cuyos items son colecciones tipadas.
            // Llamamos groupBy sobre cada una de ellas.
            $reGrouped = collect($result->all())->map(function ($subCollection) use ($nextGroups, $preserveKeys) {
                // Cada $subCollection es ya una instancia de tu colección tipada
                return $subCollection->groupBy($nextGroups, $preserveKeys);
            });

            return $this->toAny($reGrouped->all());
        }

        return $result;
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
     * Si `$value` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo (o el separador), se trabaja sobre la representación en array.
     *
     * @param (callable(TItem, int|string): mixed)|string $value
     * @return string
     */
    public function implode($value, $glue = null): string
    {
        if (self::useAsCallable($value)) {
            return collect($this->items)->implode($value, $glue);
        }

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

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = ''): string
    {
        return collect($this->toArray())->join($glue, $finalGlue);
    }

//    public function keyBy()
//    {
//        //
//    }

    /**
     * @return CollectionAny
     */
    public function keys(): CollectionAny
    {
        return new CollectionAny(array_keys($this->items));
    }

    /**
     * @param (callable(TItem, int|string): bool)|null $callback
     * @param mixed $default
     * @return TItem|mixed
     */
    public function last(?callable $callback = null, $default = null): mixed
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
     * El callback recibe los items originales de la colección y su clave. Los valores devueltos se
     * guardan tal cual en la `CollectionAny` resultante (no se convierten a array).
     *
     * @param callable(TItem, int|string): mixed $callback
     * @return CollectionAny
     */
    public function map(callable $callback): CollectionAny
    {
        $collResult = collect($this->items)->map($callback);
        return $this->toAny($collResult->all());
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

    /**
     * El callback recibe los items originales de la colección y su clave, y debe devolver un array
     * `[clave => valor]`. Los valores se guardan tal cual en la `CollectionAny` resultante.
     *
     * @param callable(TItem, int|string): array $callback
     * @return CollectionAny
     */
    public function mapWithKeys(callable $callback): CollectionAny
    {
        $collResult = collect($this->items)->mapWithKeys($callback);
        return $this->toAny($collResult->all());
    }

    /**
     * Si `$callback` es un callable, recibe los items originales de la colección (no arrays).
     * Si es un campo, se calcula sobre la representación en array.
     *
     * @param (callable(TItem): mixed)|string|null $callback
     * @return mixed
     */
    public function max($callback = null): mixed
    {
        if (self::useAsCallable($callback)) {
            return collect($this->items)->max($callback);
        }

        return collect($this->toArray())->max($callback);
    }

//    public function median()
//    {
//        //
//    }

    /**
     * Combina los items originales de la colección con los de `$items` (mismo criterio que Laravel: las
     * claves string se sobrescriben y las numéricas se añaden al final). No se serializa ningún item: las
     * entidades, DTO o Value Objects de ambos lados se conservan como objetos. Si `$items` es una
     * colección del paquete o de Laravel, se usan sus items (`all()`).
     *
     * El resultado puede contener items de cualquier tipo, por lo que siempre devuelve una `CollectionAny`.
     *
     * @param iterable|\Illuminate\Contracts\Support\Arrayable|AbstractCollectionBase $items
     * @return CollectionAny
     */
    public function merge($items): CollectionAny
    {
        return $this->toAny(array_merge($this->items, $this->getArrayableItems($items)));
    }

//    public function mergeRecursive()
//    {
//        //
//    }

    /**
     * Si `$callback` es un callable, recibe los items originales de la colección (no arrays).
     * Si es un campo, se calcula sobre la representación en array.
     *
     * @param (callable(TItem): mixed)|string|null $callback
     * @return mixed
     */
    public function min($callback = null): mixed
    {
        if (self::useAsCallable($callback)) {
            return collect($this->items)->min($callback);
        }

        return collect($this->toArray())->min($callback);
    }

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

    /**
     * Separa la colección en dos: los items que cumplen el callback y los que no. Se conservan las claves.
     *
     * El callback recibe los items originales de la colección (las mismas instancias, no arrays) y su clave.
     *
     * @param callable(TItem, int|string): bool $callback
     * @return array{0: static, 1: static}
     */
    public function partition(callable $callback): array
    {
        $passed = [];
        $failed = [];

        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                $passed[$key] = $item;
            } else {
                $failed[$key] = $item;
            }
        }

        return [$this->fromItems($passed), $this->fromItems($failed)];
    }

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
    public function pluck($value, $key = null): CollectionAny
    {
        $relationName = $value;

        $arr   = $this->toArray();
        $value = $this->normalizeDotPath($arr, $value);
        if (! is_null($key)) {
            $key = $this->normalizeDotPath($arr, $key);
        }

        $result = collect($arr)->pluck($value, $key)->toArray();

        if (! $this->isInstanceOfRelatable() || is_null($this->with)) {
            return CollectionAny::fromArray($result);
        }

        if (str_contains($relationName, '.')) {
            return CollectionAny::fromArray($result);
        }

        $with = is_array($this->with) ? $this->with : [$this->with];

        $newWith   = null;
        $newIsFull = null;
        foreach ($with as $relKey => $rel) {

            if (is_string($relKey)) {
                [$relKey, $isFull] = $this->getInfoFromRelationWithFlag($relKey);

                if ($relKey === $relationName) {
                    $newWith   = $rel;
                    $newIsFull = $isFull;
                    break;
                }
            } else {
                $arrayRels = explode('.', $rel);
                $firstRel  = $arrayRels[0];
                [$firstRel, $isFull] = $this->getInfoFromRelationWithFlag($firstRel);

                if ($firstRel === $relationName) {
                    unset($arrayRels[0]);
                    $newWith   = implode('.', $arrayRels);
                    $newIsFull = $isFull;
                    break;
                }
            }
        }
        $newWith = (empty($newWith)) ? null : $newWith;

        return CollectionAny::fromArray($result, $newWith, $newIsFull);
    }

    private function normalizeDotPath(array $data, string $path): string
    {
        if (empty($data)) {
            return $path;
        }

        $segments   = explode('.', $path);
        $current    = $data[array_key_first($data)]; // primer item, aunque la colección no tenga la clave 0 (tras where, skip, filter...)
        $normalized = [];

        foreach ($segments as $segment) {
            // Si llegamos a un valor escalar, no podemos seguir profundizando
            if (! is_array($current)) {
                $normalized[] = $segment;
                break;
            }

            // Si el segmento no existe en el nivel actual, intenta su versión snake_case
            if (! array_key_exists($segment, $current)) {
                $snake = Str::snake($segment);

                if (array_key_exists($snake, $current)) {
                    $segment = $snake;
                }
            }

            $normalized[] = $segment;

            // Avanza un nivel más si el valor actual es un array
            $next = $current[$segment] ?? null;
            if (is_array($next)) {
                // Si el siguiente nivel es una colección (índices numéricos), usamos el primer elemento
                if (array_is_list($next)) {
                    $current = $next[array_key_first($next)] ?? [];
                } else {
                    $current = $next;
                }
            } else {
                // No hay más niveles
                $current = [];
            }
        }

        return implode('.', $normalized);
    }


    /**
     * @template T of AbstractCollectionBase
     *
     * @param string $field
     * @param class-string<T> $to
     * @return T
     */
    public function pluckTo(string $field, string $to): AbstractCollectionBase
    {
        return $this->pluck($field)->toCollection($to);
    }

//    public function pop()
//    {
//        //
//    }

    /**
     * Añade un item al principio de la colección, validando su tipo igual que `push()`/`put()`.
     *
     * @param TItem $value
     * @param array-key|null $key
     * @return static
     */
    public function prepend($value, $key = null): static
    {
        $this->assertItemTypeResolved($value);
        $this->items = Arr::prepend($this->items, ...(func_num_args() > 1 ? func_get_args() : [$value]));

        return $this;
    }

//    public function pull()
//    {
//        //
//    }

    /**
     * @param TItem ...$values
     * @return static
     */
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->assertItemTypeResolved($value);
            $this->items[] = $value;
        }
        return $this;
    }

    /**
     * Añade o sustituye el item de la clave `$key`. El tipo se valida en `offsetSet()`.
     *
     * @param array-key|null $key
     * @param TItem $value
     * @return static
     */
    public function put($key, $value): static
    {
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

    /**
     * Create a collection of all elements that do not pass a given truth test. Keys are preserved.
     *
     * Si `$callback` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un valor, se eliminan los items cuya representación en array (o valor escalar, en los Value
     * Objects) sea igual (`==`) a ese valor. En ambos casos se devuelven las instancias originales.
     *
     * @param (callable(TItem, int|string): bool)|mixed $callback
     * @return static
     */
    public function reject($callback = true): static
    {
        if (self::useAsCallable($callback)) {
            return $this->fromItems(collect($this->items)->reject($callback)->all());
        }

        $collResult = collect($this->toArray())->reject($callback);
        return $this->fromResultKeys($collResult->all());
    }

//    public function replace()
//    {
//        //
//    }

//    public function replaceRecursive()
//    {
//        //
//    }

    /**
     * Invierte el orden de los items conservando las claves y las instancias originales.
     *
     * En una colección paginada se conserva la paginación: solo se invierte la página actual, no el
     * conjunto completo.
     *
     * @return static
     */
    public function reverse(): static
    {
        return $this->fromItems(array_reverse($this->items, true), keepPagination: true);
    }

//    public function search()
//    {
//        //
//    }

    /**
     * Devuelve, para cada item, un array con solo los campos indicados, conservando las claves (igual que
     * en Laravel). Genera items nuevos, por lo que siempre devuelve una `CollectionAny`.
     *
     * @param string|array $keys
     * @return CollectionAny
     */
    public function select($keys): CollectionAny
    {
        $keys       = is_array($keys) ? $keys : func_get_args();
        $collResult = collect($this->toArray())->select($keys);
        return $this->toAny($collResult->all());
    }

//    public function shift()
//    {
//        //
//    }

//    public function shuffle()
//    {
//        //
//    }

    /**
     * Omite los primeros `$count` items conservando las claves y las instancias originales.
     *
     * @return static
     */
    public function skip(int $count): static
    {
        return $this->slice($count);
    }

//    public function skipUntil()
//    {
//        //
//    }

//    public function skipWhile()
//    {
//        //
//    }

    /**
     * Devuelve un tramo de la colección conservando las claves y las instancias originales.
     *
     * @return static
     */
    public function slice(int $offset, ?int $length = null): static
    {
        return $this->fromItems(array_slice($this->items, $offset, $length, true));
    }

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
     * Ordena la colección conservando las claves y las instancias originales.
     *
     * Si `$callback` es un callable (comparador), recibe los items originales de la colección (no arrays).
     * Sin callback (o con flags de ordenación), se ordena sobre la representación en array (`toArray()`).
     *
     * En una colección paginada se conserva la paginación: solo se ordena la página actual, no el
     * conjunto completo.
     *
     * @param (callable(TItem, TItem): int)|int|null $callback
     * @return static
     */
    public function sort($callback = null): static
    {
        if (self::useAsCallable($callback)) {
            return $this->fromItems(collect($this->items)->sort($callback)->all(), keepPagination: true);
        }

        $collResult = collect($this->toArray())->sort($callback);
        return $this->fromResultKeys($collResult->all(), keepPagination: true);
    }

    /**
     * Ordena la colección conservando las claves y las instancias originales.
     *
     * Si `$callback` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo, se ordena sobre la representación en array (`toArray()`).
     *
     * También admite un array de comparaciones, igual que Laravel (`sortBy([['name', 'asc'], ['age', 'desc']])`
     * o `sortBy([fn($a, $b) => ..., ['name', 'asc']])`): los campos se comparan sobre la representación en
     * array y los comparadores callable reciben los items originales.
     *
     * En una colección paginada se conserva la paginación: solo se ordena la página actual, no el
     * conjunto completo.
     *
     * @param (callable(TItem, int|string): mixed)|string|array $callback
     * @param $options
     * @param $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false): static
    {
        if (is_array($callback) && ! self::useAsCallable($callback)) {
            return $this->sortByMany($callback, $options);
        }

        if (self::useAsCallable($callback)) {
            return $this->fromItems(collect($this->items)->sortBy($callback, $options, $descending)->all(), keepPagination: true);
        }

        $collResult = collect($this->toArray())->sortBy($callback, $options, $descending);
        return $this->fromResultKeys($collResult->all(), keepPagination: true);
    }

    /**
     * Igual que `sortBy()` pero en orden descendente. Con un array de comparaciones, todas se aplican
     * en orden descendente (igual que Laravel).
     *
     * En una colección paginada se conserva la paginación: solo se ordena la página actual, no el
     * conjunto completo.
     *
     * @param (callable(TItem, int|string): mixed)|string|array $callback
     * @param $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR): static
    {
        if (is_array($callback) && ! self::useAsCallable($callback)) {
            foreach ($callback as $index => $key) {
                $comparison       = Arr::wrap($key);
                $comparison[1]    = 'desc';
                $callback[$index] = $comparison;
            }
        }

        return $this->sortBy($callback, $options, true);
    }

    /**
     * Ordena por varias comparaciones (mismo algoritmo que `Collection::sortByMany()` de Laravel). Los
     * campos se leen de la representación en array (`toArray()`) y los comparadores callable reciben los
     * items originales.
     */
    private function sortByMany(array $comparisons, int $options): static
    {
        $arrays = $this->toArray();
        $items  = $this->items;

        uksort($items, function ($keyA, $keyB) use ($comparisons, $options, $arrays) {
            foreach ($comparisons as $comparison) {
                $comparison = Arr::wrap($comparison);
                $prop       = $comparison[0];
                $ascending  = Arr::get($comparison, 1, true) === true || Arr::get($comparison, 1, true) === 'asc';

                if (self::useAsCallable($prop)) {
                    $result = $prop($this->items[$keyA], $this->items[$keyB]);
                } else {
                    $values = [data_get($arrays[$keyA], $prop), data_get($arrays[$keyB], $prop)];

                    if (! $ascending) {
                        $values = array_reverse($values);
                    }

                    if (($options & SORT_FLAG_CASE) === SORT_FLAG_CASE) {
                        $result = ($options & SORT_NATURAL) === SORT_NATURAL
                            ? strnatcasecmp((string)$values[0], (string)$values[1])
                            : strcasecmp((string)$values[0], (string)$values[1]);
                    } else {
                        $result = match ($options) {
                            SORT_NUMERIC       => intval($values[0]) <=> intval($values[1]),
                            SORT_STRING        => strcmp((string)$values[0], (string)$values[1]),
                            SORT_NATURAL       => strnatcmp((string)$values[0], (string)$values[1]),
                            SORT_LOCALE_STRING => strcoll((string)$values[0], (string)$values[1]),
                            default            => $values[0] <=> $values[1],
                        };
                    }
                }

                if ($result === 0) {
                    continue;
                }

                return $result;
            }

            return 0;
        });

        return $this->fromItems($items, keepPagination: true);
    }

    /**
     * Ordena en orden descendente sobre la representación en array (`toArray()`), conservando las claves
     * y las instancias originales.
     *
     * En una colección paginada se conserva la paginación: solo se ordena la página actual, no el
     * conjunto completo.
     *
     * @param $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR): static
    {
        $collResult = collect($this->toArray())->sortDesc($options);
        return $this->fromResultKeys($collResult->all(), keepPagination: true);
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
     * Devuelve los primeros (o, si es negativo, los últimos) `$limit` items, conservando las claves
     * y las instancias originales.
     *
     * @param int $limit
     * @return static
     */
    public function take($limit): static
    {
        return $this->fromItems(collect($this->items)->take($limit)->all());
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
     * Convierte cada item a su representación en array:
     * - con `$forMakeArray = false` (`toArray()`): la representación pública del item (en los DTO incluye
     *   los campos computed y cualquier override de `toArray()`). Es la que usan todos los métodos que
     *   comparan, filtran, ordenan o extraen campos (`where*`, `sort*`, `unique`, `contains`, `pluck`...).
     * - con `$forMakeArray = true` (`toArrayMake()`): en los items `MakeArrayable` (DTO), solo los datos
     *   necesarios para reconstruirlos con `fromArray()`. En el resto de items es igual que `toArray()`.
     */
    private function buildArray(bool $forMakeArray): array
    {
        $result = [];
        foreach ($this->items as $key => $item) {
            $item         = match (true) {
                $item instanceof MakeArrayable && $forMakeArray => $item->toMakeArray(),
                $item instanceof ArrayConvertible               => $item->toArray(),
                $item instanceof AbstractValueObject            => $item->value,
                default                                         => $item,
            };
            $result[$key] = $item;
        }
        return $result;
    }

    /**
     * Representación en array pensada para reconstruir los items con `fromArray()`. Solo se usa en
     * `toCollection()`; para comparar, filtrar o extraer campos se usa siempre `toArray()`.
     */
    private function toArrayMake(): array
    {
        return $this->buildArray(true);
    }

    public function toArray(): array
    {
        return $this->buildArray(false);
    }

    public function toArrayDynamic($toArrayMethod, ...$params): array
    {
        return array_map(fn($item) => $item->$toArrayMethod(...$params), $this->items);
    }

    public function toPlainArray(): array
    {
        return Serialization::jsonToArray($this->toArray());
    }

    public function toPlainObject(): object|array
    {
        return Serialization::jsonToObject($this->toArray());
    }

    public function toCollect(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * @template T of AbstractCollectionBase
     *
     * @param class-string<T> $collectionClass
     * @return T
     */
    public function toCollection(string $collectionClass): AbstractCollectionBase
    {
        if (is_subclass_of($collectionClass, Relatable::class)) {
            return $collectionClass::fromArray($this->toArrayMake(), $this->with, $this->isFull);
        }
        return $collectionClass::fromArray($this->toArrayMake());
    }

    /**
     * @param $options
     * @return false|string
     */
    public function toJson($options = 0): string|false
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
     * Elimina los items duplicados conservando las claves y las instancias originales.
     *
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Sin clave (o con un campo), se compara sobre la representación en array.
     *
     * @param (callable(TItem, int|string): mixed)|string|null $key
     * @param $strict
     * @return static
     */
    public function unique($key = null, $strict = false): static
    {
        if (self::useAsCallable($key)) {
            return $this->fromItems(collect($this->items)->unique($key, $strict)->all());
        }

        $collResult = collect($this->toArray())->unique($key, $strict);
        return $this->fromResultKeys($collResult->all());
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
     * Reindexa las claves conservando las instancias originales.
     *
     * En una colección paginada se conserva la paginación: solo se reindexa la página actual.
     *
     * @return static
     */
    public function values(): static
    {
        return $this->fromItems(array_values($this->items), keepPagination: true);
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
     * Si `$key` es un callable, equivale a `filter($key)`: recibe los items originales de la colección
     * (no arrays) y su clave.
     *
     * Si es un campo, filtra sobre la representación en array (props y valores de los Value Objects),
     * pero devuelve las instancias originales conservando las claves.
     *
     * @param (callable(TItem, int|string): bool)|string|null $key
     * @param $operator
     * @param $value
     * @return static
     */
    public function where($key, $operator = null, $value = null): static
    {
        if (self::useAsCallable($key)) {
            return $this->filter($key);
        }

        $collResult = collect($this->toArray())->where(...func_get_args());
        return $this->fromResultKeys($collResult->all());
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
     * Filtra sobre la representación en array (props y valores de los Value Objects), pero devuelve
     * las instancias originales conservando las claves. Si `$values` es una colección del paquete
     * (por ejemplo, `CollectionIds`), se compara con su representación en array.
     *
     * @param $key
     * @param iterable|AbstractCollectionBase $values
     * @param $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false): static
    {
        $collResult = collect($this->toArray())->whereIn($key, self::comparableValues($values), $strict);
        return $this->fromResultKeys($collResult->all());
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
     * Filtra sobre la representación en array (props y valores de los Value Objects), pero devuelve
     * las instancias originales conservando las claves. Si `$values` es una colección del paquete
     * (por ejemplo, `CollectionIds`), se compara con su representación en array.
     *
     * @param string $key
     * @param iterable|AbstractCollectionBase $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false): static
    {
        $collResult = collect($this->toArray())->whereNotIn($key, self::comparableValues($values), $strict);
        return $this->fromResultKeys($collResult->all());
    }

//    public function whereNotInStrict()
//    {
//        //
//    }

    /**
     * @param $key
     * @return static
     */
    public function whereNotNull($key = null): static
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
