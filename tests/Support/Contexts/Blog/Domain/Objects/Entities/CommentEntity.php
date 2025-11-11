<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\CommentCollection;

final class CommentEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $content,
        public readonly IdVo          $user_id,
        public readonly IdNullVo      $post_id,
        public readonly IdNullVo      $comment_id,
    )
    {
    }

    #[RelationOf(UserEntity::class)]
    public function user(): ?UserEntity
    {
        return $this->getRelation();
    }

    #[RelationOf(PostEntity::class)]
    public function post(): ?PostEntity
    {
        return $this->getRelation();
    }

    #[RelationOf(CommentEntity::class)]
    public function comment(): ?CommentEntity
    {
        return $this->getRelation();
    }

    #[RelationOf(CommentCollection::class)]
    public function responses(): CommentCollection
    {
        return $this->getRelation();
    }
}
