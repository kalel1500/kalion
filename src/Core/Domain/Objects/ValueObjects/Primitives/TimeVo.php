<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractTimeVo;

class TimeVo extends AbstractTimeVo
{
    /**
     * @var CarbonImmutable
     */
    public $value;

    protected bool $nullable = false;
}
