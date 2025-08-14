<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelTimestampNull;

final class PostEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString         $title,
        public readonly ModelString         $content,
        public readonly ModelString         $slug,
        public readonly ModelId             $user_id,
        public readonly ModelTimestampNull  $created_at,
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            ModelId::from($data['id'] ?? null),
            ModelString::new($data['title']),
            ModelString::new($data['content']),
            ModelString::new($data['slug']),
            ModelId::new($data['user_id']),
            ModelTimestampNull::new($data['created_at']),
        );
    }

    protected function toArrayProperties(): array
    {
        return [
            'id'         => $this->id->value(),
            'title'      => $this->title->value(),
            'content'    => $this->content->value(),
            'slug'       => $this->slug->value(),
            'user_id'    => $this->user_id->value(),
            'created_at' => $this->created_at->value(),
        ];
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
