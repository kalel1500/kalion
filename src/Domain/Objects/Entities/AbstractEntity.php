<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use ReflectionClass;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionEntity;
use Thehouseofel\Kalion\Domain\Services\Relation;

abstract class AbstractEntity implements Arrayable, JsonSerializable
{
    public static ?array       $databaseFields = null;
    protected string           $primaryKey     = 'id';
    protected bool             $incrementing   = true;
    protected ?array           $with           = null;
    protected ?array           $withFull       = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected array            $relations = [];

    abstract protected static function createFromArray(array $data): static;

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

        $self                 = static::createFromArray($data);
        $self->originalArray  = $data;
        $self->isFull         = $isFull;
        $self->with($with);
        return $self;
    }

    abstract protected function toArrayProperties(): array;

    protected function toArrayCalculatedProps(): array
    {
        return [];
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
        [$relation, $defaultIsFull] = Relation::getInfoFromRelationWithFlag('flag:' . config('kalion.entity_calculated_props_mode'));

        $data   = $this->toArrayProperties();
        $isFull = $this->isFull ?? $defaultIsFull;
        if ($isFull === true) {
            $data = array_merge($data, $this->toArrayCalculatedProps());
        } elseif (is_string($isFull)) {
            $data = array_merge($data, $this->{$isFull}());
        }

        if ($this->withFull) {
            foreach ($this->withFull as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = Relation::getInfoFromRelationWithFlag($relation);
                $relationData               = $this->$relation()?->toArray();
                $data[str_snake($relation)] = $relationData;
            }
        }

        return $data;
    }

    public function toArrayDb($keepId = false): array
    {
        $array = $this->toArrayProperties();
        if (!is_null(static::$databaseFields)) {
            return array_keep($array, static::$databaseFields);
        }
        if ($this->incrementing && !$keepId) unset($array[$this->primaryKey]);
        unset($array['created_at']);
        unset($array['updated_at']);
        return $array;
    }

    public function toArrayWithout(array $fields, $fromArrayDb = false): array
    {
        $array = $fromArrayDb ? $this->toArrayDb() : $this->toArrayProperties();
        foreach ($fields as $field) {
            unset($array[$field]);
        }

        return $array;
    }

    public function toArrayWith(array $fields, $fromArrayDb = false): array
    {
        $arrayValues = $fromArrayDb ? $this->toArrayDb() : $this->toArrayProperties();
        foreach ($arrayValues as $key => $value) {
            if (!in_array($key, $fields)) unset($arrayValues[$key]);
        }
        return $arrayValues;
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
            [$first, $isFull] = Relation::getInfoFromRelationWithFlag($first, $isFull);

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

        $ref       = new ReflectionClass(static::class);
        $attribute = $ref->getMethod($first)->getAttributes(RelationOf::class)[0] ?? null;

        /** @var AbstractEntity|AbstractCollectionEntity $class */
        $class                   = $attribute->newInstance()->class;
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

    public function getRelation()
    {
        $name = debug_backtrace()[1]['function'];
        if (!array_key_exists($name, $this->relations)) {
            throw EntityRelationException::relationNotSetInEntitySetup($name, static::class);
        }
        return $this->relations[$name];
    }
}
