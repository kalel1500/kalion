<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts;

use Illuminate\Support\Arr;
use stdClass;
use Thehouseofel\Kalion\Core\Domain\Contracts\ArrayResolvable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Concerns\HasRelatableOptions;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Contracts\Relatable;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\PaginationDataDto;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Support\Internal\Serialization;

abstract class AbstractCollectionEntity extends AbstractCollectionBase implements Relatable, ArrayResolvable
{
    use HasRelatableOptions;

    /**
     * Datos de paginación. Solo existen si la colección se creó a partir de una página (`fromArray()` con
     * `current_page`); es la única fuente de verdad de `isPaginate()`.
     */
    protected ?PaginationDataDto $paginationData = null;

    /**
     * @return AbstractEntity|null
     */
    public function first(?callable $callback = null, $default = null): mixed
    {
        return parent::first(...func_get_args());
    }

    public function jsonSerialize(): array
    {
        if (is_null($this->paginationData)) {
            return parent::jsonSerialize();
        }
        return [
            'last_page' => $this->paginationData->lastPage,
            'last_row'  => $this->paginationData->total,
            'data'      => $this->toArray(),
        ];
    }

    public function toArrayDb(): array
    {
        return $this->toArrayDynamic(__FUNCTION__);
    }

    public function toArrayWith(array $fields): array
    {
        return $this->toArrayDynamic(__FUNCTION__, $fields);
    }

    protected static function fromArrayBase(?array $data, string|array|null $with, bool|string|null $isFull, bool $resolve): static
    {
        $data ??= [];

        if (! is_null($with) && ($with === '' || is_array($with) && in_array('', $with))) {
            throw new InvalidValueException(sprintf('$with can not contain empty values on <%s>:<%s>. Maybe you can see the class AbstractEntity::setFirstRelation', class_basename(static::class), 'fromData'));
        }

        $paginationData = null;
        if (array_key_exists('current_page', $data)) {
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

        $first = Arr::first($data);
        if (is_object($first) && get_class($first) === stdClass::class) {
            $data = Serialization::jsonToArray($data);
        }

        /** @var class-string<AbstractEntity> $entity */
        $entity = static::resolveItemType();
        $array  = [];
        foreach ($data as $key => $item) {
            if ($item instanceof $entity) {
                $array[$key] = $item;
            } else {
                $createdEntity = $resolve ? $entity::resolveFromArray($item, $with, $isFull) : $entity::fromArray($item, $with, $isFull);
                $array[$key]   = $createdEntity;
            }
        }
        $collection                 = new static($array);
        $collection->paginationData = $paginationData;
        $collection->with           = $with;
        $collection->isFull         = $isFull;
        return $collection;
    }

    /**
     * A null value represents an empty collection.
     */
    public static function fromArray(?array $data, string|array|null $with = null, bool|string|null $isFull = null): static
    {
        return static::fromArrayBase($data, $with, $isFull, false);
    }

    /**
     * A null value represents an empty collection.
     */
    public static function resolveFromArray(?array $data, string|array|null $with = null, bool|string|null $isFull = null): static
    {
        return static::fromArrayBase($data, $with, $isFull, true);
    }

    public function isPaginate(): bool
    {
        return ! is_null($this->paginationData);
    }

    public function paginationData(): ?PaginationDataDto
    {
        return $this->paginationData;
    }

    /**
     * Descarta la información de paginación (se usa al derivar colecciones que ya no se corresponden con la página).
     */
    protected function clearPagination(): void
    {
        $this->paginationData = null;
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
                if ($hasInfoApply && ! $item[1]) return $value;
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
