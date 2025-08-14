<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\AbstractEnumVo;

final class StatusPluckKeyVo extends AbstractEnumVo
{
    const code = 'code';
    const id = 'id';

    protected ?array $permittedValues = [
        self::code,
        self::id,
    ];

    public function __construct(string $value)
    {
        parent::__construct($value);
    }

    public static function code(): static
    {
        return new static(static::code);
    }

    public static function id(): static
    {
        return new static(static::id);
    }
}
