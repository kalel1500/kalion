<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractEnumVo;

final class StatusPluckFieldVo extends ContractEnumVo
{
    const id = 'id';
    const name = 'name';

    protected ?array $permittedValues = [
        self::id,
        self::name,
    ];

    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function id(): static
    {
        return new static(static::id);
    }

    public static function name(): static
    {
        return new static(static::name);
    }
}
