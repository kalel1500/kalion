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
    public static $databaseFields = null; // TODO PHP8 property type (?array)
    protected $primaryKey = 'id';
    protected $incrementing = true;

    protected $with;
    protected $withFull;
    protected $isFull;
    protected $originalArray;
    protected $originalObject;
    protected $isFromQuery;

    /**
     * @param array $data
     * @return static // TODO PHP8 static return type
     */
    abstract protected static function createFromArray(array $data);

    /**
     * @param Model|object $item
     * @return array
     */
    protected static function createFromObject($item): array
    {
        return [];
    }

    /**
     * @param array|null $data
     * @param string|array|null $with
     * @param bool|string|null $isFull
     * @return static|null // TODO PHP8 static return type
     */
    public static function fromArray(?array $data, $with = null, $isFull = null)
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

    /**
     * @param Model|object|null $item
     * @param string|array|null $with
     * @param bool|string|null $isFull
     * @return static // TODO PHP8 static return type
     */
    public static function fromObject($item, $with = null, $isFull = null)
    {
        if (is_null($item)) return null;

        $data                   = static::createFromObject($item);
        $self                   = static::createFromArray($data);
        $self->isFromQuery      = true;
        $self->originalArray    = json_decode(json_encode($item), true);
        $self->originalObject   = $item;
        $self->isFull           = $isFull;
        $self->with($with);
        return $self;
    }

    /**
     * @param array|Model|null $value // TODO PHP8 union types
     * @return static|null // TODO PHP8 static return type
     */
    public static function fromRelationData($value)
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
     * @return static|null // TODO PHP8 static return type
     */
    public static function createFake(array $overwriteParams = null)
    {
        return null;
    }

    public function toArray(): array
    {
        [$relation, $defaultIsFull] = getInfoFromRelationWithFlag('flag:'.config('kalion.entity_calculated_props_mode'));

        $data = $this->toArrayProperties();
        $isFull = $this->isFull ?? $defaultIsFull;
        if ($isFull === true) {
            $data = array_merge($data, $this->toArrayCalculatedProps());
        } elseif (is_string($isFull)) {
            $data = array_merge($data, $this->{$isFull}());
        }

        if ($this->withFull) {
            foreach ($this->withFull as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
                [$relation, $isFull] = getInfoFromRelationWithFlag($relation);
                $relationData = optional($this->$relation())->toArray(); // TODO PHP8 - nullsafe operator
                $data[strToSnake($relation)] = $relationData;
            }
        }

        return $data;
    }

    public function toArrayDb($keepId = false): array
    {
        $array = $this->toArrayProperties();
        if (!is_null(static::$databaseFields)) {
            return arrayKeepKeys($array, static::$databaseFields);
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

    /**
     * @param string|array $relations
     * @return $this
     */
    public function with($relations)
    {
        if (!$relations) return $this;

        $relations = is_array($relations) ? $relations : [$relations];
        $firstRelations = [];
        $firstRelationsFull = [];

        foreach ($relations as $key => $rel) {

            if (is_null($rel)) continue;

            $currentRel = ($isKey = is_string($key)) ? $key : $rel;

            $currentRels = explode('.', $currentRel);
            $first = $currentRels[0];
            unset($currentRels[0]);
            $hasRelsAfterPoint = ($relsAfterPoint = implode('.', $currentRels)) !== '';

            $last = ($isKey)
                ? ($hasRelsAfterPoint ? [$relsAfterPoint => $rel] : $rel)
                : ($hasRelsAfterPoint ? $relsAfterPoint : null);

            $isFull = null;
            $firstFull = $first;
            [$first, $isFull] = getInfoFromRelationWithFlag($first, $isFull);

            $firstRelations[] = $first;
            $firstRelationsFull[] = $firstFull;
            $this->setFirstRelation($first);
            $this->setLastRelation($first, $last, $isFull);
        }

//        $this->originalArray = null;
        $this->with = $firstRelations;
        $this->withFull = $firstRelationsFull;
        return $this;
    }

    /**
     * @param string $first
     * @return void
     */
    private function setFirstRelation(string $first)
    {
        $setRelation = 'set'.ucfirst($first);
        if ($this->isFromQuery) {
            $relationData = $this->originalObject->$first;
        } else {
            $relationName = strToSnake($first);
            if (!array_key_exists($relationName, $this->originalArray)) {
                throw new NotFoundRelationDefinitionException($relationName,static::class);
            }
            $relationData = $this->originalArray[$relationName];
        }

        $this->$setRelation($relationData);
    }

    /**
     * @param string $first
     * @param string|array $last // TODO PHP8 - Union types
     * @param bool|string|null $isFull
     */
    private function setLastRelation(string $first, $last, $isFull)
    {
        // if (empty($last)) return; // OLD
        // $last = (is_array($last)) ? $last : [$last]; // OLD

        $isEntity = is_subclass_of($this->$first, ContractEntity::class);
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

    public function setRelation($data, string $name, string $class)
    {
        $this->$name = $class::fromRelationData($data);
    }
}
