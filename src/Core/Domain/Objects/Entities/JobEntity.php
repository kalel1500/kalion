<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IntVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

class JobEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $queue,
        public readonly StringVo      $payload,
        public readonly IntVo         $attempts,
        public readonly IntNullVo     $reserved_at,
        public readonly IntVo         $available_at,
        public readonly StringVo      $created_at
    )
    {
    }
}
