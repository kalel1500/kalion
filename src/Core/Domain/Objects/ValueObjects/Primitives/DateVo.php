<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractDateVo;

class DateVo extends AbstractDateVo
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
