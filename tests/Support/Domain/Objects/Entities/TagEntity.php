<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\PostCollection;

final class TagEntity extends AbstractEntity
{
    private readonly string $type_name;
    private readonly string $type_slug;

    public function __construct(
        public readonly ModelId|ModelIdNull $id,
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

    protected function toArrayCalculatedProps(): array
    {
        return [
            'type_name' => $this->type_name(),
            'type_slug' => $this->type_slug(),
        ];
    }

    #[RelationOf(PostCollection::class)]
    public function posts(): PostCollection
    {
        return $this->getRelation();
    }

    #[RelationOf(TagTypeEntity::class)]
    public function tagType(): ?TagTypeEntity
    {
        return $this->getRelation();
    }

    public function type_name(): string
    {
        return $this->type_name ??= $this->tagType()->name->value();
    }

    public function type_slug(): string
    {
        return $this->type_slug ??= $this->tagType()->slug();
    }
}
