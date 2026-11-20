<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Carbon\CarbonImmutable;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractDateVo;

class DateVo extends AbstractDateVo
{
    /**
     * @var CarbonImmutable
     */
    public $value;

    protected bool $nullable = false;
}
