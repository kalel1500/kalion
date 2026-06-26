<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBaseEnumVo;

/**
 * @deprecated This class is deprecated and will be removed in future versions. Please use php native enum  instead.
 */
class AbstractEnumVo extends AbstractBaseEnumVo
{
    /**
     * @var string
     */
    public $value;

    protected bool $nullable = false;
}
