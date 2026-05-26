<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\TabulatorFilterDto;

#[CollectionOf(TabulatorFilterDto::class)]
class TabulatorFilterCollection extends AbstractCollectionDto
{
    /**
     * Tabulator codifica los filtros como urlencode(json_encode($filters))
     */
    public static function fromTabulator(string|array|null $value): ?static
    {
        if (is_null($value)) return null;

        if (is_string($value)) {
            $decoded = json_decode(urldecode($value), true);
            return static::fromArray($decoded ?? null);
        }

        return static::fromArray($value);
    }

    public function toTabulator(): string
    {
        return urlencode(json_encode($this->toArray()));
    }

    public function getByField(string $field): ?TabulatorFilterDto
    {
        return $this->first(fn(TabulatorFilterDto $f) => $f->field === $field);
    }

    public function hasField(string $field): bool
    {
        return $this->getByField($field) !== null;
    }

    public function except(string ...$fields): static
    {
        return $this->filter(
            fn(TabulatorFilterDto $f) => !in_array($f->field, $fields, true)
        );
    }
}
