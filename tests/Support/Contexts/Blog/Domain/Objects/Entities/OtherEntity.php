<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

final class OtherEntity extends AbstractEntity
{
    public $otherData;

    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $title,
        public readonly StringVo      $content,
    )
    {
    }

    protected function props(): array
    {
        return [...parent::props(), 'otherData' => $this->otherData];
    }
}
