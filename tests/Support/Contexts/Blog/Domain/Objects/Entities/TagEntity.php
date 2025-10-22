<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\PostCollection;

final class TagEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
        public readonly StringVo      $code,
        public readonly IdVo          $tag_type_id,
    )
    {
    }

    #[Computed]
    public function type_name(): string
    {
        return $this->computed(fn() => $this->tagType()->name->value());
    }

    #[Computed]
    public function type_slug(): string
    {
        return $this->computed(fn() => $this->tagType()->slug());
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
}
