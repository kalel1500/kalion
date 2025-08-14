<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Abstracts\AbstractModelIdZero;

final class ModelIdZeroNull extends AbstractModelIdZero
{
    protected const IS_MODEL = true;
}
