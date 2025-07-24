<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;
use Thehouseofel\Kalion\Domain\Traits\EntityHasPermissions;
use Thehouseofel\Kalion\Domain\Traits\HasGuard;

class UserEntity extends ContractEntity
{
    use EntityHasPermissions, HasGuard;

    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
        public readonly ModelString     $email,
        public readonly ModelStringNull $email_verified_at
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
            ModelString::new($data['email']),
            ModelStringNull::new($data['email_verified_at']),
        );
    }

    protected static function createFromChildArray(array $data, array $newFields): static
    {
        return new static(
            ModelId::from($data['id']),
            ModelString::new($data['name']),
            ModelString::new($data['email']),
            ModelStringNull::new($data['email_verified_at']),
            ...$newFields
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'                => $this->id->value(),
            'name'              => $this->name->value(),
            'email'             => $this->email->value(),
            'email_verified_at' => $this->email_verified_at->value(),
        ];
    }

    protected function toArrayPropertiesFromChild(array $newFields): array
    {
        return [
            'id'                => $this->id->value(),
            'name'              => $this->name->value(),
            'email'             => $this->email->value(),
            'email_verified_at' => $this->email_verified_at->value(),
            ...$newFields
        ];
    }
}
