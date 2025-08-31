<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Attributes\Computed;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Traits\ParsesRelationFlags;

abstract class AbstractEntity implements Arrayable, JsonSerializable
{
    use ParsesRelationFlags;

    private static array       $computedCache  = [];
    protected static ?array    $databaseFields = null;
    protected static string    $primaryKey     = 'id';
    protected static bool      $incrementing   = true;

    protected ?array           $with           = null;
    protected ?array           $withFull       = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected array            $relations      = [];
    protected array            $computed       = [];

    abstract protected static function make(array $data): static;

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

    abstract protected function props(): array;

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

    /**
     * La definimos sin lógica para que las clases que existen sepan cuál sobreescriben
     */
    public static function createFake(array $overwriteParams = null): static|null
    {
        return null;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
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

        if ($this->withFull) {
            foreach ($this->withFull as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = $this->getInfoFromRelationWithFlag($relation);
                $relationData               = $this->$relation()?->toArray();
                $data[str_snake($relation)] = $relationData;
            }
        }

        return $data;
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

    public function toArrayWithout(array $fields, $fromArrayDb = false): array
    {
        $array = $fromArrayDb ? $this->toArrayDb() : $this->props();
        foreach ($fields as $field) {
            unset($array[$field]);
        }

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

    protected function computed(callable $value)
    {
        $name = debug_backtrace()[1]['function'];
        return $this->computed[$name] ??= $value();
    }

    public function with(string|array|null $relations): static
    {
        if (!$relations) return $this;

        $relations          = is_array($relations) ? $relations : [$relations];
        $firstRelations     = [];
        $firstRelationsFull = [];

        foreach ($relations as $key => $rel) {

            if (is_null($rel)) continue;

            $currentRel = ($isKey = is_string($key)) ? $key : $rel;

            $currentRels = explode('.', $currentRel);
            $first       = $currentRels[0];
            unset($currentRels[0]);
            $hasRelsAfterPoint = ($relsAfterPoint = implode('.', $currentRels)) !== '';

            $last = ($isKey)
                ? ($hasRelsAfterPoint ? [$relsAfterPoint => $rel] : $rel)
                : ($hasRelsAfterPoint ? $relsAfterPoint : null);

            $isFull    = null;
            $firstFull = $first;
            [$first, $isFull] = $this->getInfoFromRelationWithFlag($first, $isFull);

            $firstRelations[]     = $first;
            $firstRelationsFull[] = $firstFull;
            $this->setFirstRelation($first);
            $this->setLastRelation($first, $last, $isFull);
        }

//        $this->originalArray = null;
        $this->with     = $firstRelations;
        $this->withFull = $firstRelationsFull;
        return $this;
    }

    private function setFirstRelation(string $first): void
    {
        $relationName = str_snake($first);
        if (!array_key_exists($relationName, $this->originalArray)) {
            throw EntityRelationException::relationNotLoadedInEloquentResult($relationName, static::class);
        }
        $relationData = $this->originalArray[$relationName];

        $ref        = new ReflectionClass(static::class); // REFLECTION - delete
        $attributes = $ref->getMethod($first)->getAttributes(RelationOf::class);

        if (empty($attributes)) {
            $class = static::class;
            throw new RequiredDefinitionException("The $class::$first method must have the #[RelationOf(...)] attribute defined.");
        }

        /** @var AbstractEntity|AbstractCollectionEntity $class */
        $class                   = $attributes[0]->newInstance()->class;
        $this->relations[$first] = $class::fromArray($relationData);
    }

    private function setLastRelation(string $first, string|array|null $last, bool|string|null $isFull): void
    {
        $isEntity     = is_subclass_of($this->relations[$first], AbstractEntity::class);
        $isCollection = is_subclass_of($this->relations[$first], AbstractCollectionEntity::class);

        if ($isEntity) {
            $this->relations[$first]->with($last);
            $this->relations[$first]->isFull = $isFull;
        }
        if ($isCollection) {
            foreach ($this->relations[$first] as $item) {
                $item->with($last);
                $item->isFull = $isFull;
            }
            $this->relations[$first]->setWith($last)->setIsFull($isFull);
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
}
