<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractEnumVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class StatusPluckFieldVo extends AbstractEnumVo
{
    const id   = 'id';
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
