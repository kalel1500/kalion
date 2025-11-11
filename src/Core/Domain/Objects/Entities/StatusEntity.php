<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

class StatusEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
        public readonly BoolVo        $finalized,
        public readonly StringVo      $code,
        public readonly StringVo      $type,
        public readonly StringNullVo  $icon
    )
    {
    }
}
