<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Enums;

interface Identifiable
{
    public static function ids(): array;
    public function getId(): int;
    public static function fromId(int $id);
}
