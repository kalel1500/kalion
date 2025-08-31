<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Domain\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity as BaseUserEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

class UserEntity extends BaseUserEntity
{
    public function __construct(
        ModelId|ModelIdNull $id,
        ModelString         $name,
        ModelString         $email,
        ModelStringNull     $email_verified_at,
        public readonly     ModelStringNull $other_field,
    )
    {
        parent::__construct($id, $name, $email, $email_verified_at);
    }

    protected static function make(array $data): static
    {
        return new static(
            ModelId::from($data['id']),
            ModelString::new($data['name']),
            ModelString::new($data['email']),
            ModelStringNull::new($data['email_verified_at']),
            ModelStringNull::new($data['other_field'] ?? 'prueba'),
        );
    }

    #[RelationOf(PostCollection::class)]
    public function posts(): PostCollection
    {
        return $this->getRelation();
    }

    #[RelationOf(CommentCollection::class)]
    public function comments(): CommentCollection
    {
        return $this->getRelation();
    }
}
