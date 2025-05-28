<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\ContractModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class TagEntity extends ContractEntity
{
    public function __construct(
        public readonly ContractModelId $id,
        public readonly ModelString     $name,
        public readonly ModelString     $code,
        public readonly ModelId         $tag_type_id,
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
            ModelString::new($data['code']),
            ModelId::new($data['tag_type_id']),
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'          => $this->id->value(),
            'name'        => $this->name->value(),
            'code'        => $this->code->value(),
            'tag_type_id' => $this->tag_type_id->value(),
        ];
    }

    public function posts(): PostCollection
    {
        return $this->getRelation('posts');
    }

    public function setPosts(array $value): void
    {
        $this->setRelation($value, 'posts', PostCollection::class);
    }

    public function tagType(): ?TagTypeEntity
    {
        return $this->getRelation('tagType');
    }

    public function setTagType(?array $value): void
    {
        $this->setRelation($value, 'tagType', TagTypeEntity::class);
    }
}
