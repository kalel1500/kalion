<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as CollectionS;
use Thehouseofel\Kalion\Domain\Contracts\ExportableEntity;
use Thehouseofel\Kalion\Domain\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Exceptions\RequiredDefinitionException;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\PaginationDataDo;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;

abstract class ContractCollectionEntity extends ContractCollectionBase implements Relatable
{
    protected const IS_ENTITY = true;
    protected string|array|null $with   = null;
    protected bool|string|null  $isFull = null;
    protected bool              $isPaginate;
    protected ?PaginationDataDo $paginationData;

    public function setWith(string|array|null $with): static
    {
        $this->with = $with;
        return $this;
    }

    public function setIsFull(bool|string|null $isFull): static
    {
        $this->isFull = $isFull;
        return $this;
    }

    public function first(): ?ContractEntity
    {
        return parent::first();
    }

    public function jsonSerialize(): array
    {
        if (!$this->isPaginate()) {
            return parent::jsonSerialize();
        }
        return [
            'last_page' => $this->paginationData->lastPage,
            'last_row'  => $this->paginationData->total,
            'data'      => $this->toArray(),
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
        $entity       = static::ENTITY;
        $isExportable = (is_subclass_of($entity, ExportableEntity::class));
        if (!$isExportable) return $data;

        $cols    = $entity::$exportMethodName();
        $newData = collect($data)->map(function ($item) use ($cols) {
            $newItem = [];
            foreach ($cols as $col) {
                $key       = $col['key'];
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

    private static function fromData(
        array|Collection|CollectionS|null $data,
        string|array|null     $with = null,
        bool|string|null      $isFull = null,
        bool                  $isEloquentBuilder = false,
        bool                  $isPaginate = false,
        ?PaginationDataDo     $paginationData = null
    ): static|null
    {
        if (is_null($data)) return null;

        if (is_null(static::ENTITY)) {
            throw new RequiredDefinitionException(sprintf('<%s> needs to define <%s> %s.', class_basename(static::class), 'ENTITY', 'constant'));
        }

        if (!is_null($with) && ($with === '' || is_array($with) && in_array('', $with))) {
            throw new InvalidValueException(sprintf('$with can not contain empty values on <%s>:<%s>. Maybe you can see the class ContractEntity::setFirstRelation', class_basename(static::class), 'fromData'));
        }

        $entity = static::ENTITY;
        $array  = [];
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
                $array[]       = $createdEntity;
            }
        }
        $collection                 = new static(...$array); // Los 3 puntos son importantes, ya que los constructores también reciben los parámetros destructurados (...)
        $collection->isPaginate     = $isPaginate;
        $collection->paginationData = $paginationData;
        $collection->with           = $with;
        $collection->isFull         = $isFull;
        return $collection;
//        dd(new static());
//        dd($result->toArray());
    }

    public static function fromArray(
        array|Collection|null $data,
        string|array|null     $with = null,
        bool|string|null      $isFull = null
    ): static|null
    {
        if (is_null($data)) return null;
        $isPaginate     = array_key_exists('current_page', $data);
        $paginationData = null;
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
            $data           = $data['data'];
        }
        return static::fromData($data, $with, $isFull, false, $isPaginate, $paginationData);
    }

    public static function fromEloquent(
        Collection|CollectionS|LengthAwarePaginator|null $queryResult,
        string|array|null                                $with = null,
        bool|string|null                                 $isFull = null,
        bool                                             $saveBuilderObject = false
    ): static|null
    {
        // $data = $response->isFromQuery() ? $response->originalObject() : $response->originalArray();
        // return static::fromData($data, $with, $response->isFromQuery(), $response->isPaginate(), $response->paginationData());

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
        return static::fromData($data, $with, $isFull, $saveBuilderObject, $isPaginate, $paginationData);
    }

    public static function fromRelationData(array|Collection $data): static|null
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

    public function setIsPaginate(bool $isPaginate): void
    {
        $this->isPaginate = $isPaginate;
    }

    public function setPaginationData(PaginationDataDo $paginationData): void
    {
        $this->paginationData = $paginationData;
    }

    public static function createFake(int $number, int $startIdOn = 1, array $overwriteParams = []): static
    {
        /** @var ContractEntity $entity */
        $entity = static::ENTITY;
        $array  = [];
        for ($i = 0; $i <= $number; $i++) {
            $newId                    = $startIdOn + $i;
            $makeValuesRandom         = function ($item) use ($newId) {
                $hasInfoApply = (is_array($item) && (count($item) === 2));
                $value        = ($hasInfoApply) ? $item[0] : $item;
                if ($hasInfoApply && !$item[1]) return $value;
                if (is_string($value)) return $value . $newId;
                if (is_int($value)) return $value + $newId;
                if (is_bool($value)) return ((bool)mt_rand(0, 1));
                return $value;
            };
            $newOverwriteParams       = array_map($makeValuesRandom, $overwriteParams);
            $newOverwriteParams['id'] = $newId;
            $array[]                  = $entity::createFake($newOverwriteParams);
        }
        return new static(...$array);
    }
}
