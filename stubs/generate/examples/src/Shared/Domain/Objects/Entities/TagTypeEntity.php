<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\ContractModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class TagTypeEntity extends ContractEntity
{
    public function __construct(
        public readonly ContractModelId $id,
        public readonly ModelString     $name,
        public readonly ModelString     $code,
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
            ModelString::new($data['code']),
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'   => $this->id->value(),
            'name' => $this->name->value(),
            'code' => $this->code->value(),
        ];
    }

    public function tags(): TagCollection
    {
        return $this->getRelation('tags');
    }

    public function setTags(array $value): void
    {
        $this->setRelation($value, 'tags', TagCollection::class);
    }
}
