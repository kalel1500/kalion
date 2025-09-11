<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Exceptions\ReflectionException;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Concerns\Relations\ParsesRelationFlags;

abstract class AbstractEntity implements Arrayable, JsonSerializable
{
    use ParsesRelationFlags;

    private static array       $makeCache      = [];
    private static array       $propsCache     = [];
    private static array       $computedCache  = [];
    protected static ?array    $databaseFields = null;
    protected static string    $primaryKey     = 'id';
    protected static bool      $incrementing   = true;

    protected ?array           $with           = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected array            $relations      = [];
    protected array            $computed       = [];

    protected static function make(array $data): static
    {
        $className = static::class;

        if (!isset(self::$makeCache[$className])) {
            $ref  = new ReflectionClass($className); // REFLECTION - cached
            $constructor = $ref->getConstructor();

            if (!$constructor) {
                throw ReflectionException::constructorMissing($className);
            }

            $params = [];

            foreach ($constructor->getParameters() as $param) {
                $name = $param->getName();
                $type = $param->getType();

                // Intersection type → no permitido
                if ($type instanceof \ReflectionIntersectionType) {
                    throw ReflectionException::intersectionTypeNotSupported($name, $className, '__construct');
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
                        'name'      => $name,
                        'class'     => $modelIdClass, // null si no aplica
                        'isModelId' => (bool) $modelIdClass,
                    ];
                    continue;
                }

                // Sin tipo, tipo primitivo (int, string, bool, etc.) → valor directo
                if ($type === null || ($type instanceof \ReflectionNamedType && $type->isBuiltin())) {
                    $params[] = [
                        'name'      => $name,
                        'class'     => null,
                        'isModelId' => false,
                    ];
                    continue;
                }

                // Named type (Class)
                if ($type instanceof \ReflectionNamedType) {
                    $class = $type->getName();
                    $params[] = [
                        'name'      => $name,
                        'class'     => $class,
                        'isModelId' => false, // para single class → usamos ::new || is_class_model_id($class)
                    ];
                    continue;
                }

                // Cualquier otro caso raro
                $params[] = [
                    'name'      => $name,
                    'class'     => null,
                    'isModelId' => false,
                ];
            }

            self::$makeCache[$className] = $params;
        }

        $args = [];

        foreach (self::$makeCache[$className] as $meta) {
            $paramName = $meta['name'];
            $class     = $meta['class'];
            $isModelId = $meta['isModelId'];
            $value     = $data[$paramName] ?? null;

            $value = match (true) {
                $isModelId || enum_exists($class)                            => $class::from($value),
                class_exists($class) && method_exists($class, method: 'new') => $class::new($value),
                default                                                      => $value,
            };

            $args[] = $value;
        }

        return new static(...$args);
    }

    protected function props(): array
    {
        $className = static::class;

        // Cachear la reflexión de propiedades públicas
        if (!isset(self::$propsCache[$className])) {
            $ref    = new ReflectionClass($this); // REFLECTION - cached
            $cached = [];

            foreach ($ref->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
                $cached[] = $property->getName();
            }

            self::$propsCache[$className] = $cached;
        }

        $props = [];

        // Recorrer los nombres ya cacheados
        foreach (self::$propsCache[$className] as $name) {
            $value = $this->{$name};

            $value = match (true) {
                is_object($value) && method_exists($value, 'value') => $value->value(),
                is_object($value) && property_exists($value, 'value') => $value->value,
                default => $value,
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
    public static function fromArray($data, string|array|null $with = null, bool|string $isFull = null)
    {
        if (is_null($data)) return null;

        $self                = static::make($data);
        $self->originalArray = $data;
        $self->isFull        = $isFull;
        $self->with($with);
        return $self;
    }

    public function toArray(): array
    {
        [$relation, $defaultIsFull] = $this->getInfoFromRelationWithFlag('flag:' . config('kalion.entity_calculated_props_mode'));

        $data   = $this->props();
        $isFull = $this->isFull ?? $defaultIsFull;
        if ($isFull === true) {
            $data = array_merge($data, $this->computedProps());
        } elseif (is_string($isFull)) {
            $data = array_merge($data, $this->computedProps($isFull));
        }

        if ($this->with) {
            foreach ($this->with as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = $this->getInfoFromRelationWithFlag($relation);
                $relationData               = $this->$relation()?->toArray();
                $data[str_snake($relation)] = $relationData;
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

                /** @var Computed $attr */
                $attr = $attrs[0]->newInstance();

                $cached[] = [
                    'name'     => $method->getName(),
                    'contexts' => $attr->contexts,
                ];
            }

            self::$computedCache[$className] = $cached;
        }

        $result = [];

        foreach (self::$computedCache[$className] as $meta) {
            // Contexto no coincide → saltar
            if ($context && !empty($meta['contexts']) && !in_array($context, $meta['contexts'], true)) {
                continue;
            }

            $name = $meta['name'];
            $result[$name] = $this->{$name}();
        }

        return $result;
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
        $isEntity     = is_subclass_of($this->relations[$relation], AbstractEntity::class);
        $isCollection = is_subclass_of($this->relations[$relation], AbstractCollectionEntity::class);

        if ($isEntity) {
            $this->relations[$relation]->with($relationRels);
            $this->relations[$relation]->isFull = $isFull;
        }
        if ($isCollection) {
            foreach ($this->relations[$relation] as $item) {
                $item->with($relationRels);
                $item->isFull = $isFull;
            }
            $this->relations[$relation]->setWith($relationRels)->setIsFull($isFull);
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
