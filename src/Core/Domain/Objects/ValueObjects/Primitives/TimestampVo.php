<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractTimestampVo;

class TimestampVo extends AbstractTimestampVo
{
    /**
     * @var string
     */
    public $value;

    protected bool $nullable = false;
}
