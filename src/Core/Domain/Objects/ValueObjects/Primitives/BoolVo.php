<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractBoolVo;

class BoolVo extends AbstractBoolVo
{
    /**
     * @var bool
     */
    public $value;

    protected bool $nullable = false;
}
