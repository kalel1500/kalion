<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\FloatVo;

final class ModelFloat extends FloatVo
{
    protected const IS_MODEL = true;
}
