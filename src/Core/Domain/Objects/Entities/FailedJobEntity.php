<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Entities;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

class FailedJobEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $uuid,
        public readonly StringVo      $connection,
        public readonly StringVo      $queue,
        public readonly StringVo      $payload,
        public readonly StringVo      $exception,
        public readonly StringVo      $failed_at
    )
    {
    }
}
