<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Abstracts\AbstractModelIdZero;

final class ModelIdZero extends AbstractModelIdZero
{
    protected const IS_MODEL = true;

    protected bool $nullable = false;

    public function __construct(int $value)
    {
        parent::__construct($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
