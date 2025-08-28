<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities;

use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\TagCollection;

final class TagTypeEntity extends AbstractEntity
{
    private readonly string $slug;

    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString         $name,
        public readonly ModelString         $code,
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

    protected function toArrayCalculatedProps(): array
    {
        return [
            'slug' => $this->slug(),
        ];
    }

    #[RelationOf(TagCollection::class)]
    public function tags(): TagCollection
    {
        return $this->getRelation();
    }


    public function slug(): string
    {
        return $this->slug ??= Str::slug($this->name);
    }
}
