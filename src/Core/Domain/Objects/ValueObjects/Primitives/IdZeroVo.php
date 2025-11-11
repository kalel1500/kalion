<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractIdZero;

final class IdZeroVo extends AbstractIdZero
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
