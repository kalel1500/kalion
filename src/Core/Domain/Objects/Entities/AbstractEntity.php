<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Entities;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Core\Domain\Concerns\Relations\ParsesRelationFlags;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayConvertible;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\SnapshotDiff;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto\ComputedMetadata;
use Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto\ReflectionConfig;
use Thehouseofel\Kalion\Core\Domain\Support\Reflection\ReflectionResolvable;

abstract class AbstractEntity implements ArrayConvertible, ArrayResolvable, JsonSerializable
{
    use ReflectionResolvable, ParsesRelationFlags;

    protected static ?array       $fillable      = null;
    protected static string       $primaryKey    = 'id';
    protected static bool         $incrementing  = true;

    protected ?array           $with      = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected array            $relations = [];
    protected array            $computed  = [];
    protected array            $domainEvents = [];

    protected static function reflectionConfig(): ReflectionConfig
    {
        return new ReflectionConfig(
            props_from_public       : false,
            allow_id_union          : true,
            allow_disable_reflection: false,
            expected_exception_class: AbstractEntity::class,
        );
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

    /**
     * @template T of array|null
     * @param T $data
     * @param array|string|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function resolveFromArray(?array $data, string|array|null $with = null, bool|string $isFull = null): ?static
    {
        if (empty($data)) return null;

        $self                = static::make($data, true);
        $self->originalArray = $data;
        $self->isFull        = $isFull;
        $self->with($with);
        return $self;
    }

    public function toArray(): array
    {
        $data   = $this->props();
        $isFull = $this->normalizeIsFull($this->isFull ?? config('kalion.entity_calculated_props_mode')); // kalion.entity_calculated_props_mode => s
        $data   = array_merge($data, $this->computedPropsForContext($isFull));

        if ($this->with) {
            foreach ($this->with as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = $this->getInfoFromRelationWithFlag($relation);
                $data[Str::snake($relation)] = $this->$relation()?->toArray();
            }
        }

        return $data;
    }

    /**
     * Normaliza los flags de la configuración ('f' => full, 's' => simple).
     * Cualquier otro string se considera un contexto.
     */
    private function normalizeIsFull(bool|string|null $isFull): bool|string
    {
        return match ($isFull) {
            'f'         => true,
            's', null   => false,
            default     => $isFull,
        };
    }

    private function computedPropsForContext(bool|string $isFull): array
    {
        return $this->computedProps(
            fn(ComputedMetadata $meta) => $this->contextMatch($isFull, $meta->contexts, $meta->addOnFull)
        );
    }

    private function contextMatch(bool|string $isFull, array $attributeContexts, bool $addOnFull): bool
    {
        // AS_ATTRIBUTE: se añade siempre (también en modo simple)
        if (in_array(Computed::AS_ATTRIBUTE, $attributeContexts)) {
            return true;
        }

        // IS_FULL: sin contextos, o con contextos + addOnFull = true
        if ($isFull === true) {
            return empty($attributeContexts) || $addOnFull;
        }

        // IS_CONTEXT: si el contexto está en contexts (independiente de addOnFull)
        if (is_string($isFull)) {
            return in_array($isFull, $attributeContexts);
        }

        // IS_SIMPLE (false): solo los AS_ATTRIBUTE
        return false;
    }

    public function toArrayDb($keepId = false): array
    {
        $array = $this->props();
        if (! is_null(static::$fillable)) {
            return Arr::only($array, static::$fillable);
        }
        if (static::$incrementing && ! $keepId) unset($array[static::$primaryKey]);
        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['deleted_at']);
        return $array;
    }

    public function toArrayWith(array $fields, $fromArrayDb = false): array
    {
        $arrayValues = $fromArrayDb ? $this->toArrayDb() : $this->props();
        foreach ($arrayValues as $key => $value) {
            if (! in_array($key, $fields)) unset($arrayValues[$key]);
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
        if (! $relations) return $this;

        $relations       = is_array($relations) ? $relations : [$relations];
        $entityRelations = [];

        foreach ($relations as $key => $segment) {

            if (is_null($segment)) continue;

            $currentSegment = ($isKey = is_string($key)) ? $key : $segment;

            $currentRels = explode('.', $currentSegment);
            $entityRel   = $currentRels[0];
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

    public function updateRelations(array $data, string|array $with): static
    {
        $this->originalArray = array_merge($this->originalArray ?? [], $data);
        return $this->with($with);
    }

    private function setCurrentRelation(string $relation): void
    {
        $relationName = Str::snake($relation);
        if (! array_key_exists($relationName, $this->originalArray)) {
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
        if (! array_key_exists($name, $this->relations)) {
            throw EntityRelationException::relationNotSetInEntitySetup($name, static::class);
        }
        return $this->relations[$name];
    }

    protected function computed(callable $value, ?string $key = null)
    {
        $cacheKey = debug_backtrace()[1]['function'];

        if (! is_null($key)) {
            $cacheKey = $cacheKey . ':' . $key;
        }

        return $this->computed[$cacheKey] ??= $value();
    }

    protected function record(object $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return object[]
     */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];
        return $events;
    }

    protected function diffSnapshots(array $oldSnapshot, array $newSnapshot): SnapshotDiff
    {
        return SnapshotDiff::between($oldSnapshot, $newSnapshot);
    }


    public static function createFake(array $overwriteParams = null): static|null
    {
        return null;
    }
}
