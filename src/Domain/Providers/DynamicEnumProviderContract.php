<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Providers;

use Thehouseofel\Kalion\Domain\Contracts\Providers\DynamicEnumProvider;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\EnumDynamicVo;

abstract class DynamicEnumProviderContract implements DynamicEnumProvider
{
    protected ?array $permittedValues = null;

    public function getPermittedValues(): array
    {
        return $this->permittedValues;
    }

    public static function newEnum(string $value): EnumDynamicVo
    {
        return EnumDynamicVo::new($value, new static());
    }
}
