<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\FloatNullVo;

final class ModelFloatNull extends FloatNullVo
{
    protected const IS_MODEL = true;
}
