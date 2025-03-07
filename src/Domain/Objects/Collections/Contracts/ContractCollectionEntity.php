<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as CollectionS;
use Illuminate\Pagination\LengthAwarePaginator;
use Thehouseofel\Kalion\Domain\Contracts\ExportableEntity;
use Thehouseofel\Kalion\Domain\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\PaginationDataDo;

abstract class ContractCollectionEntity extends ContractCollectionBase implements Relatable
{
    protected const IS_ENTITY = true;
    protected $with = null;
    protected $isFull = null;
    protected $isPaginate;
    protected $paginationData;

    public function setWith($with): ContractCollectionEntity
    {
        $this->with = $with;
        return $this;
    }

    /**
     * @param bool|string|null $isFull
     * @return ContractCollectionEntity
     */
    public function setIsFull($isFull): ContractCollectionEntity
    {
        $this->isFull = $isFull;
        return $this;
    }

    public function first(): ?ContractEntity
    {
        return $this->items[0] ?? null;
    }

    public function jsonSerialize(): array
    {
        if (!$this->isPaginate()) {
            return parent::jsonSerialize();
        }
        return [
            'last_page' => $this->paginationData()->lastPage(),
            'last_row' => $this->paginationData()->total(),
            'data' => $this->toArray(),
        ];
    }


    /*public static function with(array $relations)
    {
        static::$with = $relations;
        return new static;
    }*/

    public function toArrayExport(callable $modifyData = null, string $exportMethodName = 'getExportColumns'): array
    {
        $data = $this->toArray();
        if (!is_null($modifyData)) {
            $data = $modifyData($data);
        }

        /** @var ContractEntity $entity */
        $entity = static::ENTITY;
        $isExportable = (is_subclass_of($entity, ExportableEntity::class));
        if (!$isExportable) return $data;

        $cols = $entity::$exportMethodName();
        $newData = collect($data)->map(function ($item) use ($cols) {
            $newItem = [];
            foreach ($cols as $col) {
                $key = $col['key'];
                $newItem[] = array_key_exists($key, $item) ? $item[$key] : ' ';
            }
            return $newItem;
        })->toArray();

        $headers = collect($cols)->pluck('name')->toArray();
        return array_merge([$headers], $newData);
    }

    public function toArrayDb(): array
    {
        return $this->toArrayDynamic(__FUNCTION__);
    }

    public function toArrayWith(array $fields): array
    {
        return $this->toArrayDynamic(__FUNCTION__, $fields);
    }

    /**
     * @param array|Collection|null $data // TODO PHP8 union types
     * @param string|array|null $with
     * @param bool|string|null $isFull
     * @param bool $isPaginate
     * @param PaginationDataDo|null $paginationData
     * @param bool $isEloquentBuilder
     * @return static|null // TODO PHP8 return static
     */
    private static function fromData(
        $data,
        $with = null,
        $isFull = null,
        bool $isEloquentBuilder = false,
        bool $isPaginate = false,
        ?PaginationDataDo $paginationData = null
    )
    {
        if (is_null($data)) return null;

        if (is_null(static::ENTITY)) {
            throw new RequiredDefinitionException(sprintf('<%s> needs to define <%s> %s.', class_basename(static::class), 'ENTITY', 'constant'));
        }

        if (!is_null($with) && ($with === '' || is_array($with) && in_array('', $with))) {
            throw new InvalidValueException(sprintf('$with can not contain empty values on <%s>:<%s>. Maybe you can see the class ContractEntity::setFirstRelation', class_basename(static::class), 'fromData'));
        }

        $entity = static::ENTITY;
        $array = [];
        foreach ($data as $item) {
            if ($item instanceof $entity) {
                $array[] = $item;
            } else {
                /*$createdEntity = $isEloquentBuilder
                    ? $entity::fromObject($item, null, $isFull)
                    : $entity::fromArray($item, null, $isFull);
                if (!is_null($with)) {
                    $createdEntity->with($with);
                }*/
                $createdEntity = $isEloquentBuilder
                    ? $entity::fromObject($item, $with, $isFull)
                    : $entity::fromArray($item, $with, $isFull);
                $array[] = $createdEntity;
            }
        }
        $collection = new static(...$array); // Los 3 puntos son importantes, ya que los constructores también reciben los parámetros destructurados (...)
        $collection->isPaginate = $isPaginate;
        $collection->paginationData = $paginationData;
        $collection->with = $with;
        $collection->isFull = $isFull;
        return $collection;
//        dd(new static());
//        dd($result->toArray());
    }

    /**
     * @param array|Collection|null $data // TODO PHP8 union types
     * @param string|array|null $with
     * @param bool|string|null $isFull
     * @return static|null // TODO PHP8 return static
     */
    public static function fromArray($data, $with = null, $isFull = null)
    {
        if (is_null($data)) return null;
        $isPaginate         = array_key_exists('current_page', $data);
        $paginationData     = null;
        if ($isPaginate) {
            $paginationData = new PaginationDataDo(
                $data['total'],
                $data['last_page'],
                intval($data['per_page']),
                $data['current_page'],
                $data['path'],
                'page',
                '--'
            );
            $data = $data['data'];
        }
        return self::fromData($data, $with, $isFull, false, $isPaginate, $paginationData);
    }

    /**
     * @param Collection|CollectionS|LengthAwarePaginator $queryResult
     * @param string|array|null $with
     * @param bool|string|null $isFull
     * @param bool $saveBuilderObject
     * @return static|null
     */
    public static function fromEloquent($queryResult, $with = null, $isFull = null, bool $saveBuilderObject = false)
    {
        // $data = $response->isFromQuery() ? $response->originalObject() : $response->originalArray();
        // return self::fromData($data, $with, $response->isFromQuery(), $response->isPaginate(), $response->paginationData());

        if (is_null($queryResult)) return null;
        $isPaginate     = is_a($queryResult, LengthAwarePaginator::class);
        $paginationData = null;
        if ($isPaginate) {
            $paginationData = new PaginationDataDo(
                $queryResult->total(),
                $queryResult->lastPage(),
                intval($queryResult->perPage()),
                $queryResult->currentPage(),
                $queryResult->path(),
                $queryResult->getPageName(),
                $queryResult->links()->toHtml()
            );
        }
        $data = $saveBuilderObject ? $queryResult : $queryResult->values()->toArray();
        return self::fromData($data, $with, $isFull, $saveBuilderObject, $isPaginate, $paginationData);
    }

    /**
     * @param array|Collection $data // TODO PHP8 union types
     * @return static
     */
    public static function fromRelationData($data)
    {
        $isFromQuery = is_object($data);
        return static::fromData($data, null, null, $isFromQuery);
    }

    public function isPaginate(): bool
    {
        return $this->isPaginate;
    }

    public function paginationData(): ?PaginationDataDo
    {
        return $this->paginationData;
    }

    public function setIsPaginate(bool $isPaginate)
    {
        $this->isPaginate = $isPaginate;
    }

    public function setPaginationData(PaginationDataDo $paginationData)
    {
        $this->paginationData = $paginationData;
    }

    /**
     * @param int $number
     * @param int $startIdOn
     * @param array|null $overwriteParams
     * @return static // TODO PHP8 return static
     */
    public static function createFake(int $number, int $startIdOn = 1, array $overwriteParams = [])
    {
        /** @var ContractEntity $entity */
        $entity = static::ENTITY;
        $array = [];
        for ($i = 0; $i <= $number; $i++) {
            $newId = $startIdOn+$i;
            $makeValuesRandom = function($item) use ($newId) {
                $hasInfoApply = (is_array($item) && (count($item) === 2));
                $value = ($hasInfoApply) ? $item[0] : $item;
                if ($hasInfoApply && !$item[1]) return $value;
                if (is_string($value)) return $value.$newId;
                if (is_int($value)) return $value+$newId;
                if (is_bool($value)) return ((bool)mt_rand(0,1));
                return $value;
            };
            $newOverwriteParams = array_map($makeValuesRandom, $overwriteParams);
            $newOverwriteParams['id'] = $newId;
            $array[] = $entity::createFake($newOverwriteParams);
        }
        return new static(...$array);
    }
}
