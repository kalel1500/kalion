<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractFloatVo;

class FloatVo extends AbstractFloatVo
{
    /**
     * @var float
     */
    public $value;

    protected bool $nullable = false;

    public function __construct(float $value)
    {
        parent::__construct($value);
    }
}
