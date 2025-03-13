<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity as BaseUserEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\ContractModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

class UserEntity extends BaseUserEntity
{
    public ModelStringNull $other_field;

    public function __construct(
        ContractModelId $id,
        ModelString     $name,
        ModelString     $email,
        ModelStringNull $email_verified_at,
        ModelStringNull $other_field,
    )
    {
        parent::__construct($id, $name, $email, $email_verified_at);
        $this->other_field = $other_field;
    }

    protected static function createFromArray(array $data): static
    {
        return parent::createFromChildArray($data, [
            ModelStringNull::new($data['other_field'] ?? 'prueba')
        ]);
    }

    protected function toArrayProperties(): array
    {
        return parent::toArrayPropertiesFromChild([
            'other_field' => $this->other_field->value(),
        ]);
    }

    public function posts(): PostCollection
    {
        return $this->getRelation('posts');
    }

    public function setPosts(array $value): void
    {
        $this->setRelation($value, 'posts', PostCollection::class);
    }

    public function comments(): CommentCollection
    {
        return $this->getRelation('comments');
    }

    public function setComments(array $value): void
    {
        $this->setRelation($value, 'comments', CommentCollection::class);
    }
}
