<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts\AbstractModelId;

final class ModelIdNull extends AbstractModelId
{
    protected const IS_MODEL = true;
}
