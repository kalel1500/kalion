<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractArrayVo;

class ArrayVo extends AbstractArrayVo
{
    /**
     * @var array
     */
    public $value;

    protected bool $nullable = false;

    public function __construct(array $value)
    {
        parent::__construct($value);
    }
}
