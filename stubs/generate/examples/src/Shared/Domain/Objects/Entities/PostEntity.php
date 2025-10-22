<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\TimestampNullVo;

final class PostEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo   $id,
        public readonly StringVo        $title,
        public readonly StringVo        $content,
        public readonly StringVo        $slug,
        public readonly IdVo            $user_id,
        public readonly TimestampNullVo $created_at,
    )
    {
    }

    #[RelationOf(UserEntity::class)]
    public function user(): ?UserEntity
    {
        return $this->getRelation();
    }

    #[RelationOf(CommentCollection::class)]
    public function comments(): CommentCollection
    {
        return $this->getRelation();
    }

    #[RelationOf(TagCollection::class)]
    public function tags(): TagCollection
    {
        return $this->getRelation();
    }
}
