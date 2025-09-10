<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts;

use Illuminate\Support\Arr;
use Thehouseofel\Kalion\Domain\Objects\Entities\Contracts\Exportable;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\Relatable;
use Thehouseofel\Kalion\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\PaginationDataDto;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\Concerns\HasRelatableOptions;

abstract class AbstractCollectionEntity extends AbstractCollectionBase implements Relatable
{
    use HasRelatableOptions;

    protected bool               $isPaginate;
    protected ?PaginationDataDto $paginationData;

    /**
     * @return AbstractEntity|null
     */
    public function first(?callable $callback = null, $default = null)
    {
        return parent::first(...func_get_args());
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

    /**
     * @experimental This method is subject to change (signature and behavior). It may be removed in future versions.
     */
    public function toArrayExport(callable $modifyData = null, string $exportMethodName = 'getExportColumns'): array
    {
        $data = $this->toArray();
        if (!is_null($modifyData)) {
            $data = $modifyData($data);
        }

        /** @var AbstractEntity $entity */
        $entity       = $this->resolvedItemType;
        $isExportable = (is_subclass_of($entity, Exportable::class));
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

    /**
     * @template T of array|null
     * @param T $data
     * @param string|array|null $with
     * @param bool|string $isFull
     * @return (T is null ? null : static)
     */
    public static function fromArray($data, string|array|null $with = null, bool|string $isFull = null)
    {
        if (is_null($data)) return null;

        if (!is_null($with) && ($with === '' || is_array($with) && in_array('', $with))) {
            throw new InvalidValueException(sprintf('$with can not contain empty values on <%s>:<%s>. Maybe you can see the class AbstractEntity::setFirstRelation', class_basename(static::class), 'fromData'));
        }

        $isPaginate     = array_key_exists('current_page', $data);
        $paginationData = null;
        if ($isPaginate) {
            $paginationData = new PaginationDataDto(
                total      : $data['total'],
                lastPage   : $data['last_page'],
                perPage    : intval($data['per_page']),
                currentPage: $data['current_page'],
                path       : $data['path'],
                pageName   : 'page',
                htmlLinks  : '--',
            );
            $data           = $data['data'];
        }

        if (is_object(Arr::first($data))) {
            $data = object_to_array($data);
        }

        /** @var class-string<AbstractEntity> $entity */
        $entity = static::resolveItemType();
        $array  = [];
        foreach ($data as $key => $item) {
            if ($item instanceof $entity) {
                $array[$key] = $item;
            } else {
                $createdEntity = $entity::fromArray($item, $with, $isFull);
                $array[$key]   = $createdEntity;
            }
        }
        $collection                 = new static($array);
        $collection->isPaginate     = $isPaginate;
        $collection->paginationData = $paginationData;
        $collection->with           = $with;
        $collection->isFull         = $isFull;
        return $collection;
    }

    public function isPaginate(): bool
    {
        return $this->isPaginate;
    }

    public function paginationData(): ?PaginationDataDto
    {
        return $this->paginationData;
    }

    public function setIsPaginate(bool $isPaginate): void
    {
        $this->isPaginate = $isPaginate;
    }

    public function setPaginationData(PaginationDataDto $paginationData): void
    {
        $this->paginationData = $paginationData;
    }

    /**
     * @experimental This method is subject to change (signature and behavior). It may be removed in future versions.
     */
    public static function createFake(int $number, int $startIdOn = 1, array $overwriteParams = []): static
    {
        /** @var AbstractEntity $entity */
        $entity = static::resolveItemType();
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
