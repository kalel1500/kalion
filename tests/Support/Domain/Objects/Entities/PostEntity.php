<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelTimestampNull;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\CommentCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\TagCollection;

final class PostEntity extends AbstractEntity
{
    private readonly int $number_comments;

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

    protected function props(): array
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

    protected function calc(): array
    {
        return [
            'number_comments' => $this->number_comments(),
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

    public function number_comments(): int
    {
        return $this->number_comments ??= $this->comments()->count();
    }
}
