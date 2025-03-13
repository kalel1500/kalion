<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts;

use Thehouseofel\Kalion\Domain\Providers\DynamicEnumProviderContract;

abstract class ContractDynamicEnumVo extends ContractEnumVo
{
    protected DynamicEnumProviderContract $dynamicEnumProvider;

    public function __construct($value, DynamicEnumProviderContract $dynamicEnumProvider)
    {
        $this->dynamicEnumProvider = $dynamicEnumProvider;
        parent::__construct($value);
    }

    protected function getPermittedValues(): ?array
    {
        return $this->dynamicEnumProvider->getPermittedValues();
    }

    public static function new($value, DynamicEnumProviderContract $dynamicEnumProvider = null): static
    {
        return new static($value, $dynamicEnumProvider);
    }

}
