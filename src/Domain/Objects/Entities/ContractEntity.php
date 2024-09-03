<?php

declare(strict_types=1);

namespace Thehouseofel\Hexagonal\Domain\Objects\Entities;

use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Thehouseofel\Hexagonal\Domain\Contracts\Arrayable;
use Thehouseofel\Hexagonal\Domain\Objects\Collections\Contracts\ContractCollectionEntity;

abstract class ContractEntity implements Arrayable, JsonSerializable
{
    public static $databaseFields = null; // TODO PHP8 property type (?array)

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
    abstract protected static function createFromObject($item): array;

    /**
     * @param array|null $data
     * @param string|array|null $with
     * @param bool|null $isFull
     * @return static|null // TODO PHP8 static return type
     */
    public static function fromArray(?array $data, $with = null, ?bool $isFull = null)
    {
        if (is_null($data)) return null;
        $self                 = static::createFromArray($data);
        $self->isFromQuery    = false;
        $self->originalArray  = $data;
        $self->originalObject = null;
        $self->isFull         = $isFull;
        if (!is_null($with)) {
            $self->with($with);
        }
        return $self;
    }

    /**
     * @param Model|object|null $item
     * @param string|array|null $with
     * @param bool|null $isFull
     * @return static|null // TODO PHP8 static return type
     */
    public static function fromObject($item, $with = null, ?bool $isFull = null)
    {
        if (is_null($item)) return null;
        $data                   = static::createFromObject($item);
        $self                   = static::createFromArray($data);
        $self->isFromQuery      = true;
        $self->originalArray    = json_decode(json_encode($item), true);
        $self->originalObject   = $item;
        $self->isFull           = $isFull;
        if (!is_null($with)) {
            $self->with($with);
        }
        return $self;
    }

    /**
     * @param array|Model $value // TODO PHP8 union types
     * @return static // TODO PHP8 static return type
     */
    public static function fromRelationData($value)
    {
        return (is_array($value))
            ? static::fromArray($value)
            : static::fromObject($value);
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
        $data = (is_null($this->isFull) || $this->isFull === true)
            ? array_merge($this->toArrayProperties(), $this->toArrayCalculatedProps())
            : $this->toArrayProperties();

        if ($this->withFull) {
            foreach ($this->withFull as $key => $rel) {
                $relation = (is_array($rel)) ? $key : $rel;
//                $isFull = true;
                if (str_contains($relation, ':')) {
                    [$relation, $flag] = explode(':', $relation);
//                    $isFull = $flag === 'f' ? true : ($flag === 's' ? false : $isFull);
                }
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
        if (!$keepId) unset($array['id']);
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
        $relations = is_array($relations) ? $relations : [$relations];
        $firstRelations = [];
        $firstRelationsFull = [];
        foreach ($relations as $key => $relationString) {

            if (is_array($relationString)) {
                $first = $key;
                $last = $relationString;
            } else {
                $array = explode('.', $relationString);
                $first = $array[0];
                unset($array[0]);
                $last = implode('.', $array);
            }

            $isFull = null;
            $firstFull = $first;
            if (str_contains($first, ':')) {
                [$first, $flag] = explode(':', $first);
                $isFull = $flag === 'f' ? true : ($flag === 's' ? false : $isFull);
            }

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
        $relationData = ($this->isFromQuery)
            ? $this->originalObject->$first
            : ($this->originalArray[strToSnake($first)] ?? $this->originalArray[$first] ?? null);

        $relationData = $relationData ?? [];
        $this->$setRelation($relationData);
    }

    /**
     * @param string $first
     * @param string|array $last // TODO PHP8 - Union types
     * @param bool|null $isFull
     */
    private function setLastRelation(string $first, $last, ?bool $isFull)
    {
        if (!empty($last)) {

            $last = (is_array($last)) ? $last : [$last];

            $isEntity = is_subclass_of($this->$first, ContractEntity::class);
            $isCollection = is_subclass_of($this->$first, ContractCollectionEntity::class);
//                dd($this->$first, $isEntity, $isCollection);
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
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
