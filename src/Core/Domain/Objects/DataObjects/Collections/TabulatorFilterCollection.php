<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\TabulatorFilterDto;

#[CollectionOf(TabulatorFilterDto::class)]
class TabulatorFilterCollection extends TabulatorDataCollection
{
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
