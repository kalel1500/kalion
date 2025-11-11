<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractTimestampVo;

class TimestampVo extends AbstractTimestampVo
{
    /**
     * @var string
     */
    public $value;

    protected bool $nullable = false;

    public function __construct(string $value, ?array $formats = null)
    {
        parent::__construct($value, $formats);
    }
}
