<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\ContractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\ContractModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

final class CommentEntity extends ContractEntity
{
    public function __construct(
        public readonly ContractModelId $id,
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

    public function user(): ?UserEntity
    {
        return $this->getRelation('user');
    }

    public function setUser(?array $value): void
    {
        $this->setRelation($value, 'user', UserEntity::class);
    }

    public function post(): ?PostEntity
    {
        return $this->getRelation('post');
    }

    public function setPost(?array $value): void
    {
        $this->setRelation($value, 'post', PostEntity::class);
    }

    public function comment(): ?CommentEntity
    {
        return $this->getRelation('comment');
    }

    public function setComment(?array $value): void
    {
        $this->setRelation($value, 'comment', CommentEntity::class);
    }

    public function responses(): CommentCollection
    {
        return $this->getRelation('responses');
    }

    public function setResponses(array $value): void
    {
        $this->setRelation($value, 'responses', CommentCollection::class);
    }

}
