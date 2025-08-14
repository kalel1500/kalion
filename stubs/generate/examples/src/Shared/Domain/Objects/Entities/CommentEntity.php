<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class CommentEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $content,
        public readonly ModelId         $user_id,
        public readonly ModelIdNull     $post_id,
        public readonly ModelIdNull     $comment_id,
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['content']),
            ModelId::new($data['user_id']),
            ModelIdNull::new($data['post_id']),
            ModelIdNull::new($data['comment_id']),
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'         => $this->id->value(),
            'content'    => $this->content->value(),
            'user_id'    => $this->user_id->value(),
            'post_id'    => $this->post_id->value(),
            'comment_id' => $this->comment_id->value(),
        ];
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
