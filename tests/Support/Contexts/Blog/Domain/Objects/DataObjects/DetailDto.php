<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\Attributes\WithParams;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\TagTypeEntity;

final class DetailDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly StringVo      $name,
        #[WithParams('comments', true)]
        public readonly PostEntity    $post,
        #[WithParams(['isFull' => true])]
        public readonly TagTypeEntity $tagType,
    )
    {
    }
}
