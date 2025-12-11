<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractDateZeroVo;

class DateZeroVo extends AbstractDateZeroVo
{
    /**
     * @var string
     */
    public $value;

    protected bool $nullable = false;

    public function __construct(string $value)
    {
        parent::__construct($value);
    }
}
