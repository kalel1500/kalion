<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractIntVo;

class IntVo extends AbstractIntVo
{
    /**
     * @var int
     */
    public $value;

    protected bool $nullable = false;

    public function __construct(int $value)
    {
        parent::__construct($value);
    }
}
