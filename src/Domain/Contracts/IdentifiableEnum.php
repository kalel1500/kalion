<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts;

interface IdentifiableEnum
{
    public static function ids(): array;
    public function getId(): int;
    public static function fromId(int $id);
}
