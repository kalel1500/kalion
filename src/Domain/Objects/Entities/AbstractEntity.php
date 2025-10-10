<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Concerns\Relations\ParsesRelationFlags;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\AbstractValueObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\JsonMethodVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractJsonVo;

abstract class AbstractEntity implements ArrayConvertible, JsonSerializable
{
    use ParsesRelationFlags;

    private static array          $constructCache = [];
    private static array          $computedCache  = [];
    protected static ?array       $databaseFields = null;
    protected static string       $primaryKey     = 'id';
    protected static bool         $incrementing   = true;
    protected static JsonMethodVo $jsonMethod     = JsonMethodVo::encodedValue;

    protected ?array           $with           = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected array            $relations      = [];
    protected array            $computed       = [];

    private static function resolveConstructorParams(): array
    {
        $className = static::class;

        if (!isset(self::$constructCache[$className])) {
            $ref  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $ref->getConstructor();

            if (!$constructor) {
                throw KalionReflectionException::constructorMissing($className);
            }

            $params = [];

            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                $type = $param->getType();

                // Intersection type → no permitido
                if ($type instanceof \ReflectionIntersectionType) {
                    throw KalionReflectionException::intersectionTypeNotSupported($name, $className, '__construct');
                }

                // Union type (ej. ModelId|ModelIdNull)
                if ($type instanceof \ReflectionUnionType) {
                    $classNames = array_map(
                        callback: fn($t) => $t->getName(),
                        array   : array_filter(
                            array   : $type->getTypes(),
                            callback: fn($t) => $t instanceof \ReflectionNamedType && !$t->isBuiltin()
                        )
                    );

                    $modelIdClass = null;
                    foreach ($classNames as $class) {
                        if (is_class_model_id($class) && !str_ends_with($class, 'Null')) {
                            $modelIdClass = $class;
                            break;
                        }
                    }

                    $params[] = [
                        'name'       => $name,
                        'class'      => $modelIdClass, // null si no aplica
                        'isModelId'  => (bool) $modelIdClass,
                        'allowsNull' => $type->allowsNull(),
                    ];
                    continue;
                }

                // Named type (Class), tipo primitivo (int, string, bool, etc.)
                if ($type instanceof \ReflectionNamedType) {
                    $params[] = [
                        'name'       => $name,
                        'class'      => $type->isBuiltin() ? null : $type->getName(),
                        'isModelId'  => false, // para single class → usamos ::new || is_class_model_id($class)
                        'allowsNull' => $type->allowsNull(),
                    ];
                    continue;
                }

                // Sin tipo
                $params[] = [
                    'name'      => $name,
                    'class'     => null,
                    'isModelId' => false,
                    'allowsNull' => true,
                ];
            }

            $newParams = [];
            foreach ($params as $meta) {
                $class     = $meta['class'];
                $isModelId = $meta['isModelId'];

                $classIsNull = $class === null;
                $isEnum      = !$classIsNull && is_a($class, class: \BackedEnum::class, allow_string: true);
                $isVo        = !$classIsNull && is_a($class, class: AbstractValueObject::class, allow_string: true);

                $makeMethod = match (true) {
                    $classIsNull          => null,
                    $isModelId || $isEnum => 'from',
                    $isVo                 => 'new',
                    default               => throw KalionReflectionException::unexpectedTypeInEntityConstructor($className, $meta['name']),
                };

                $propsMethod = match (true) {
                    $isVo   => 'value',
                    default => null,
                };

                $newParams[] = [
                    ...$meta,
                    'makeMethod' => $makeMethod,
                    'propsMethod' => $propsMethod,
                    'propsIsEnum' => $isEnum,
                ];
            }

            self::$constructCache[$className] = $newParams;
        }

        return self::$constructCache[$className];
    }

    protected static function make(array $data): static
    {
        $args = [];

        foreach (self::resolveConstructorParams() as $meta) {
            $paramName  = $meta['name'];
            $class      = $meta['class'];
            $allowsNull = $meta['allowsNull'];
            $method     = $meta['makeMethod'];
            $value      = $data[$paramName] ?? null;

            try {
                $value = match (true) {
                    ($allowsNull && $value === null) || $method === null || ($value instanceof $class)  => $value,
                    default                                                                             => $class::$method($value),
                };
            } catch (\Throwable $th) {
                throw KalionReflectionException::failedToHydrateUsingFromArray(static::class, $paramName, $class, $value, $th->getMessage());
            }

            $args[] = $value;
        }

        return new static(...$args);
    }

    protected function props(): array
    {
        $props = [];

        // Recorrer los nombres ya cacheados
        foreach (self::resolveConstructorParams() as $meta) {
            $name   = $meta['name'];
            $method = $meta['propsMethod'];
            $isEnum = $meta['propsIsEnum'];
            $value  = $this->{$name};

            $value = match (true) {
                $isEnum          => $value?->value,
                $method === null => $value,
                default          => $value?->{$method}($value),
            };

            $props[$name] = $value;
        }

        return $props;
    }

    /**
     * @template T of array|null
     * @param T $data
     * @param array|string|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string $isFull = null): ?static
    {
        if (empty($data)) return null;

        $self                = static::make($data);
        $self->originalArray = $data;
        $self->isFull        = $isFull;
        $self->with($with);
        return $self;
    }

    public function toArray(): array
    {
        $data   = $this->props();
        $isFull = $this->isFull ?? config('kalion.entity_calculated_props_mode'); // kalion.entity_calculated_props_mode => s
        if ($isFull === true) {
            $data = array_merge($data, $this->computedProps());
        } elseif (is_string($isFull)) {
            $data = array_merge($data, $this->computedProps($isFull));
        }

        if ($this->with) {
            foreach ($this->with as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = $this->getInfoFromRelationWithFlag($relation);
                $data[str_snake($relation)] = $this->$relation()?->toArray();
            }
        }

        return $data;
    }

    private function computedProps(?string $context = null): array
    {
        $className = static::class;

        // Cachear los métodos con #[Computed]
        if (!isset(self::$computedCache[$className])) {
            $ref    = new ReflectionClass($this); // REFLECTION - cached
            $cached = [];

            foreach ($ref->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $attrs = $method->getAttributes(Computed::class);

                if (empty($attrs)) {
                    continue;
                }

                $returnType = $method->getReturnType();

                if (!($returnType instanceof \ReflectionNamedType)) {
                    throw KalionReflectionException::wrongComputedReturnType();
                }

                $returnClass = $returnType->getName();
                $propMethod = match (true) {
                    is_a($returnClass, class: AbstractJsonVo::class,      allow_string: true) => static::$jsonMethod->value,
                    is_a($returnClass, class: AbstractValueObject::class, allow_string: true) => 'value',
                    is_a($returnClass, class: ArrayConvertible::class,    allow_string: true) => 'toArray',
                    default                                                                   => null,
                };

                /** @var Computed $attr */
                $attr = $attrs[0]->newInstance();

                $cached[] = [
                    'name'      => $method->getName(),
                    'contexts'  => is_string($attr->contexts) ? [$attr->contexts] : $attr->contexts,
                    'addOnFull' => $attr->addOnFull,
                    'method'    => $propMethod,
                    'isEnum'    => is_a($returnClass, class: \BackedEnum::class, allow_string: true),
                ];
            }

            self::$computedCache[$className] = $cached;
        }

        $result = [];

        foreach (self::$computedCache[$className] as $meta) {
            $contexts  = $meta['contexts'];
            $addOnFull = $meta['addOnFull'];

            if (!$this->contextMatch($context, $contexts, $addOnFull)) {
                continue;
            }

            $name = $meta['name'];
            $method = $meta['method'];
            $value = $this->{$name}();
            $result[$name] = match (true) {
                $meta['isEnum']  => $value->value,
                $method !== null => $value->{$method}(),
                default          => $value,
            };
        }

        return $result;
    }

    private function contextMatch(?string $selectedContext, array $attributeContexts, bool $addOnFull): bool
    {
        if (in_array(Computed::AS_ATTRIBUTE, $attributeContexts)) {
            return true;
        }

        $isFull = is_null($selectedContext);

        // IS_FULL: sin contextos, o con contextos + addOnFull = true
        if ($isFull && (empty($attributeContexts) || $addOnFull)) {
            return true;
        }

        // IS_CONTEXT: si el contexto está en contexts (independiente de addOnFull)
        if (!$isFull && in_array($selectedContext, $attributeContexts)) {
            return true;
        }

        return false;
    }

    public function toArrayDb($keepId = false): array
    {
        $array = $this->props();
        if (!is_null(static::$databaseFields)) {
            return array_keep($array, static::$databaseFields);
        }
        if (static::$incrementing && !$keepId) unset($array[static::$primaryKey]);
        unset($array['created_at']);
        unset($array['updated_at']);
        return $array;
    }

    public function toArrayWith(array $fields, $fromArrayDb = false): array
    {
        $arrayValues = $fromArrayDb ? $this->toArrayDb() : $this->props();
        foreach ($arrayValues as $key => $value) {
            if (!in_array($key, $fields)) unset($arrayValues[$key]);
        }
        return $arrayValues;
    }

    public function toArrayWithout(array $fields, $fromArrayDb = false): array
    {
        $array = $fromArrayDb ? $this->toArrayDb() : $this->props();
        foreach ($fields as $field) {
            unset($array[$field]);
        }

        return $array;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function with(string|array|null $relations): static
    {
        if (!$relations) return $this;

        $relations      = is_array($relations) ? $relations : [$relations];
        $entityRelations = [];

        foreach ($relations as $key => $segment) {

            if (is_null($segment)) continue;

            $currentSegment = ($isKey = is_string($key)) ? $key : $segment;

            $currentRels = explode('.', $currentSegment);
            $entityRel       = $currentRels[0];
            unset($currentRels[0]);
            $hasRelsAfterPoint = ($relsAfterPoint = implode('.', $currentRels)) !== '';

            $subRels = ($isKey)
                ? ($hasRelsAfterPoint ? [$relsAfterPoint => $segment] : $segment)
                : ($hasRelsAfterPoint ? $relsAfterPoint : null);

            $entityRelations[] = $entityRel;
            [$entityRelName, $isFull] = $this->getInfoFromRelationWithFlag($entityRel);
            $this->setCurrentRelation($entityRelName);
            $this->setDeepRelations($entityRelName, $subRels, $isFull);
        }

//        $this->originalArray = null;
        $this->with = $entityRelations;
        return $this;
    }

    private function setCurrentRelation(string $relation): void
    {
        $relationName = str_snake($relation);
        if (!array_key_exists($relationName, $this->originalArray)) {
            throw EntityRelationException::relationNotLoadedInEloquentResult($relationName, static::class);
        }
        $relationData = $this->originalArray[$relationName];

        $ref        = new ReflectionClass(static::class); // REFLECTION - delete
        $attributes = $ref->getMethod($relation)->getAttributes(RelationOf::class);

        if (empty($attributes)) {
            $class = static::class;
            throw new RequiredDefinitionException("The $class::$relation method must have the #[RelationOf(...)] attribute defined.");
        }

        /** @var AbstractEntity|AbstractCollectionEntity $class */
        $class                      = $attributes[0]->newInstance()->class;
        $this->relations[$relation] = $class::fromArray($relationData);
    }

    private function setDeepRelations(string $relation, string|array|null $relationRels, bool|string|null $isFull): void
    {
        /** @var AbstractEntity|AbstractCollectionEntity $relationItem */
        $relationItem = $this->relations[$relation];

        $isEntity     = is_subclass_of($relationItem, AbstractEntity::class);
        $isCollection = is_subclass_of($relationItem, AbstractCollectionEntity::class);

        if ($isEntity) {
            $relationItem->with($relationRels);
            $relationItem->isFull = $isFull;
        }
        if ($isCollection) {
            foreach ($relationItem as $entity) {
                $entity->with($relationRels);
                $entity->isFull = $isFull;
            }
            $relationItem->setWith($relationRels)->setIsFull($isFull);
        }
    }

    protected function getRelation()
    {
        $name = debug_backtrace()[1]['function'];
        if (!array_key_exists($name, $this->relations)) {
            throw EntityRelationException::relationNotSetInEntitySetup($name, static::class);
        }
        return $this->relations[$name];
    }

    protected function computed(callable $value)
    {
        $name = debug_backtrace()[1]['function'];
        return $this->computed[$name] ??= $value();
    }


    public static function createFake(array $overwriteParams = null): static|null
    {
        return null;
    }
}
