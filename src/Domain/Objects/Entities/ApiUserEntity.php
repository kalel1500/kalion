<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Traits\EntityHasPermissions;
use Thehouseofel\Kalion\Domain\Traits\HasGuard;

class ApiUserEntity extends AbstractEntity
{
    use EntityHasPermissions, HasGuard;

    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
    )
    {
    }

    protected static function make(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
        );
    }

    protected static function makeWith(array $data, array $newFields): static
    {
        return new static(
            ModelId::from($data['id']),
            ModelString::new($data['name']),
            ...$newFields
        );
    }

    protected function props(): array
    {
        return [
            'id'      => $this->id->value(),
            'name'    => $this->name->value(),
        ];
    }
}
