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
     * La información de paginación no se conserva (igual que al reconstruir con `fromArray()`), ya que
     * deja de corresponderse con los items resultantes.
     *
     * @param array<array-key, TItem> $items
     * @return static
     */
    private function fromItems(array $items): static
    {
        $new        = clone $this;
        $new->items = $items;

        if ($new instanceof AbstractCollectionEntity) {
            $new->setIsPaginate(false);
        }

        return $new;
    }

    /**
     * Crea una nueva instancia con los items originales correspondientes a las claves de `$result`,
     * respetando su orden.
     *
     * Permite calcular un resultado sobre la representación en array (comparando escalares, props y
     * valores de los Value Objects) y devolver después las instancias originales en lugar de reconstruirlas.
     * Solo es válido si la operación de Laravel usada para obtener `$result` conserva las claves.
     *
     * @param array<array-key, mixed> $result
     * @return static
     */
    private function fromResultKeys(array $result): static
    {
        $items = [];
        foreach (array_keys($result) as $key) {
            $items[$key] = $this->items[$key];
        }

        return $this->fromItems($items);
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

    private function getArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        } elseif ($items instanceof AbstractCollectionBase) {
            return $items->toArrayMake();
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
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo o un valor, se busca sobre la representación en array (props y valores de los
     * Value Objects). Los strings nunca se tratan como callables (`'count'`, `'date'`... son campos).
     *
     * @param (callable(TItem, int|string): bool)|string|mixed $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function contains($key, $operator = null, $value = null)
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
     * Devuelve los items que no están en `$items`, comparando sobre la representación en array
     * (por el campo `$field` o por el item completo). Se devuelven las instancias originales,
     * con las claves reindexadas.
     *
     * @param AbstractCollectionBase $items
     * @param string|null $field
     * @return static
     */
    public function diff($items, ?string $field = null)
    {
        if (! is_null($field)) {
            $result     = [];
            $dictionary = $items->pluck($field);
            foreach ($this->toArrayMake() as $key => $item) {
                if (! $dictionary->contains($item[$field])) {
                    $result[$key] = $item;
                }
            }
        } else {
            $array1 = array_map('json_encode', $this->toArrayMake());
            $array2 = array_map('json_encode', $items->toArrayMake());

            // array_diff conserva las claves de $array1
            $result = array_diff($array1, $array2);
        }

        return $this->fromResultKeys($result)->values();
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
     *
     * @param AbstractCollectionBase|array $items
     * @return static
     */
    public function diffKeys($items)
    {
        $array1 = $this->toArrayMake();
        $array2 = $this->getArrayableItems($items);
        return $this->fromResultKeys(array_diff_key($array1, $array2));
    }

    /**
     * Determine if an item is not contained in the collection.
     *
     * @param mixed $key
     * @param mixed $operator
     * @param mixed $value
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
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo, se compara sobre la representación en array (props y valores de los Value Objects).
     *
     * @param (callable(TItem, int|string): bool)|string $key
     * @param $operator
     * @param $value
     * @return bool
     */
    public function every($key, $operator = null, $value = null)
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
     * Equivale a `where(...)->first()`, igual que en Laravel: admite un campo (con o sin operador) o un
     * callable, y devuelve el item original o `null`.
     *
     * @param (callable(TItem, int|string): bool)|string $key
     * @param $operator
     * @param $value
     * @return TItem|null
     */
    public function firstWhere($key, $operator = null, $value = null)
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
     * Aplana la representación en array de la colección (props y valores de los Value Objects).
     *
     * Genera items nuevos (escalares), que no son del tipo de la colección, por lo que siempre
     * devuelve una `CollectionAny`.
     *
     * @param $depth
     * @return CollectionAny
     */
    public function flatten($depth = INF)
    {
        $collResult = collect($this->toArrayMake())->flatten($depth);
        return $this->toAny($collResult->toArray());
    }

    /**
     * Intercambia claves y valores de la representación en array de la colección. Requiere que los
     * valores sean escalares (por ejemplo, colecciones de Value Objects).
     *
     * Los nuevos valores son las claves antiguas, que no son del tipo de la colección, por lo que siempre
     * devuelve una `CollectionAny`.
     *
     * @return CollectionAny
     */
    public function flip()
    {
        return $this->toAny(array_flip($this->toArrayMake()));
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
     * Si `$groupBy` (o el primer nivel, si es un array) es un callable, recibe los items originales de la
     * colección (no arrays) y su clave. Si es un campo, se agrupa sobre la representación en array.
     * En ambos casos cada grupo contiene las mismas instancias originales.
     *
     * @param (callable(TItem, int|string): array-key)|string|array $groupBy
     * @param $preserveKeys
     * @return CollectionAny
     */
    public function groupBy($groupBy, $preserveKeys = false)
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
            $mapped = collect($this->toArrayMake())
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
    public function implode($value, $glue = null)
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

    /**
     * Join all items from the collection using a string. The final items can use a separate glue string.
     *
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public function join($glue, $finalGlue = '')
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
     * El callback recibe los items originales de la colección y su clave. Los valores devueltos se
     * guardan tal cual en la `CollectionAny` resultante (no se convierten a array).
     *
     * @param callable(TItem, int|string): mixed $callback
     * @return CollectionAny
     */
    public function map(callable $callback)
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
    public function mapWithKeys(callable $callback)
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
    public function max($callback = null)
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
     * Combina la representación en array de la colección con `$items`. El resultado puede contener
     * items de cualquier tipo, por lo que siempre devuelve una `CollectionAny`.
     *
     * @param iterable|\Illuminate\Contracts\Support\Arrayable $items
     * @return CollectionAny
     */
    public function merge($items)
    {
        $collResult = collect($this->toArray())->merge($items);
        return $this->toAny($collResult->toArray());
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
    public function min($callback = null)
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
    public function pluck($value, $key = null)
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
    public function pluckTo(string $field, string $to)
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
    public function prepend($value, $key = null)
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
     * @param ...$values
     * @return static
     */
    public function push(...$values)
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
    public function put($key, $value)
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

        $collResult = collect($this->toArrayMake())->reject($callback);
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
     * @return static
     */
    public function reverse(): static
    {
        return $this->fromItems(array_reverse($this->items, true));
    }

//    public function search()
//    {
//        //
//    }

    /**
     * Devuelve, para cada item, un array con solo los campos indicados (reindexado). Genera items nuevos,
     * por lo que siempre devuelve una `CollectionAny`.
     *
     * @param string|array $keys
     * @return CollectionAny
     */
    public function select($keys)
    {
        $keys       = is_array($keys) ? $keys : func_get_args();
        $collResult = collect($this->toArray())
            ->select($keys)
            ->values();
        return $this->toAny($collResult->toArray());
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
     * Sin callback (o con flags de ordenación), se ordena sobre la representación en array.
     *
     * @param (callable(TItem, TItem): int)|int|null $callback
     * @return static
     */
    public function sort($callback = null)
    {
        if (self::useAsCallable($callback)) {
            return $this->fromItems(collect($this->items)->sort($callback)->all());
        }

        $collResult = collect($this->toArrayMake())->sort($callback);
        return $this->fromResultKeys($collResult->all());
    }

    /**
     * Ordena la colección conservando las claves y las instancias originales.
     *
     * Si `$callback` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Si es un campo (o un array de comparaciones), se ordena sobre la representación en array.
     *
     * Atención: en la variante con array de comparaciones (`sortBy([['name', 'asc'], fn($a, $b) => ...])`),
     * los comparadores callable reciben la representación en array de cada item (`toMakeArray()`/`toArray()`),
     * no las instancias originales. El resultado sí contiene las instancias originales.
     *
     * @param (callable(TItem, int|string): mixed)|string|array $callback
     * @param $options
     * @param $descending
     * @return static
     */
    public function sortBy($callback, $options = SORT_REGULAR, $descending = false)
    {
        if (self::useAsCallable($callback)) {
            return $this->fromItems(collect($this->items)->sortBy($callback, $options, $descending)->all());
        }

        $collResult = collect($this->toArrayMake())->sortBy($callback, $options, $descending);
        return $this->fromResultKeys($collResult->all());
    }

    /**
     * Igual que `sortBy()` pero en orden descendente.
     *
     * @param (callable(TItem, int|string): mixed)|string|array $callback
     * @param $options
     * @return static
     */
    public function sortByDesc($callback, $options = SORT_REGULAR)
    {
        return $this->sortBy($callback, $options, true);
    }

    /**
     * Ordena en orden descendente sobre la representación en array, conservando las claves y las
     * instancias originales.
     *
     * @param $options
     * @return static
     */
    public function sortDesc($options = SORT_REGULAR)
    {
        $collResult = collect($this->toArrayMake())->sortDesc($options);
        return $this->fromResultKeys($collResult->all());
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
    public function take($limit)
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
    public function toCollection(string $collectionClass)
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
     * Elimina los items duplicados conservando las claves y las instancias originales.
     *
     * Si `$key` es un callable, recibe los items originales de la colección (no arrays) y su clave.
     * Sin clave (o con un campo), se compara sobre la representación en array.
     *
     * @param (callable(TItem, int|string): mixed)|string|null $key
     * @param $strict
     * @return static
     */
    public function unique($key = null, $strict = false)
    {
        if (self::useAsCallable($key)) {
            return $this->fromItems(collect($this->items)->unique($key, $strict)->all());
        }

        $collResult = collect($this->toArrayMake())->unique($key, $strict);
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
     * @return static
     */
    public function values()
    {
        return $this->fromItems(array_values($this->items));
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
    public function where($key, $operator = null, $value = null)
    {
        if (self::useAsCallable($key)) {
            return $this->filter($key);
        }

        $collResult = collect($this->toArrayMake())->where(...func_get_args());
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
     * las instancias originales conservando las claves.
     *
     * @param $key
     * @param $values
     * @param $strict
     * @return static
     */
    public function whereIn($key, $values, $strict = false)
    {
        $collResult = collect($this->toArrayMake())->whereIn($key, $values, $strict);
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
     * las instancias originales conservando las claves.
     *
     * @param string $key
     * @param array $values
     * @param bool $strict
     * @return static
     */
    public function whereNotIn($key, $values, $strict = false)
    {
        $collResult = collect($this->toArrayMake())->whereNotIn($key, $values, $strict);
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
