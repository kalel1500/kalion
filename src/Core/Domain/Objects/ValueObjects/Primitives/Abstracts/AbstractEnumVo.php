<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBaseEnumVo;

class AbstractEnumVo extends AbstractBaseEnumVo
{
    /**
     * @var string
     */
    public $value;

    protected bool $nullable = false;
}
