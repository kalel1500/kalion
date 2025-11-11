<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractEnumVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class StatusPluckKeyVo extends AbstractEnumVo
{
    const code = 'code';
    const id   = 'id';

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
