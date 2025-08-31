<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities;

use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Attributes\Computed;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\TagCollection;

final class TagTypeEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString         $name,
        public readonly ModelString         $code,
    )
    {
    }

    protected static function make(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['name']),
            ModelString::new($data['code']),
        );
    }

    #[RelationOf(TagCollection::class)]
    public function tags(): TagCollection
    {
        return $this->getRelation();
    }

    #[Computed]
    public function slug(): string
    {
        return $this->computed(fn() => Str::slug($this->name));
    }
}
