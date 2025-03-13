<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Thehouseofel\Kalion\Domain\Contracts\Arrayable;
use Thehouseofel\Kalion\Domain\Exceptions\Database\NotFoundRelationDefinitionException;
use Thehouseofel\Kalion\Domain\Exceptions\Database\UnsetRelationException;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

abstract class ContractEntity implements Arrayable, JsonSerializable
{
    public static ?array       $databaseFields = null;
    protected string           $primaryKey     = 'id';
    protected bool             $incrementing   = true;
    protected ?array           $with           = null;
    protected ?array           $withFull       = null;
    protected bool|string|null $isFull;
    protected array            $originalArray;
    protected ?object          $originalObject;
    protected bool             $isFromQuery;

    abstract protected static function createFromArray(array $data): static;

    /**
     * @param Model|object $item
     * @return array
     */
    protected static function createFromObject($item): array
    {
        return [];
    }

    public static function fromArray(?array $data, array|string|null $with = null, bool|string $isFull = null): static|null
    {
        if (is_null($data)) return null;

        $self                 = static::createFromArray($data);
        $self->isFromQuery    = false;
        $self->originalArray  = $data;
        $self->originalObject = null;
        $self->isFull         = $isFull;
        $self->with($with);
        return $self;
    }

    public static function fromObject(?object $item, string|array|null $with = null, bool|string|null $isFull = null): static|null
    {
        if (is_null($item)) return null;

        $data                 = static::createFromObject($item);
        $self                 = static::createFromArray($data);
        $self->isFromQuery    = true;
        $self->originalArray  = json_decode(json_encode($item), true);
        $self->originalObject = $item;
        $self->isFull         = $isFull;
        $self->with($with);
        return $self;
    }

    public static function fromRelationData(array|Model|null $value): static|null
    {
        return is_object($value) ? static::fromObject($value) : static::fromArray($value);
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

    public function toArray(): array
    {
        [$relation, $defaultIsFull] = get_info_from_relation_with_flag('flag:' . config('kalion.entity_calculated_props_mode'));

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
                [$relation, $isFull] = get_info_from_relation_with_flag($relation);
                $relationData                = $this->$relation()?->toArray();
                $data[strToSnake($relation)] = $relationData;
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
            [$first, $isFull] = get_info_from_relation_with_flag($first, $isFull);

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
        $setRelation = 'set' . ucfirst($first);
        if ($this->isFromQuery) {
            $relationData = $this->originalObject->$first;
        } else {
            $relationName = strToSnake($first);
            if (!array_key_exists($relationName, $this->originalArray)) {
                throw new NotFoundRelationDefinitionException($relationName, static::class);
            }
            $relationData = $this->originalArray[$relationName];
        }

        $this->$setRelation($relationData);
    }

    private function setLastRelation(string $first, string|array|null $last, bool|string|null $isFull): void
    {
        // if (empty($last)) return; // OLD
        // $last = (is_array($last)) ? $last : [$last]; // OLD

        $isEntity     = is_subclass_of($this->$first, ContractEntity::class);
        $isCollection = is_subclass_of($this->$first, ContractCollectionEntity::class);

        if ($isEntity) {
            $this->$first->with($last);
            $this->$first->isFull = $isFull;
        }
        if ($isCollection) {
            foreach ($this->$first as $item) {
                $item->with($last);
                $item->isFull = $isFull;
            }
            $this->$first->setWith($last)->setIsFull($isFull);
        }
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getRelation(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new UnsetRelationException($name, static::class);
        }
        return $this->$name;
    }

    public function setRelation($data, string $name, string $class): void
    {
        $this->$name = $class::fromRelationData($data);
    }
}
