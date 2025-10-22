<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

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
