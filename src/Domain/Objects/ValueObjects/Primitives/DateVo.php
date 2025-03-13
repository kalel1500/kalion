<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractDateVo;

class DateVo extends ContractDateVo
{
    protected bool $nullable = false;

    public function __construct(string $value, ?array $formats = null)
    {
        parent::__construct($value, $formats);
    }

    public function value(): string
    {
        return $this->value;
    }
}
