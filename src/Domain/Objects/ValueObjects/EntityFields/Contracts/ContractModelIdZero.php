<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Contracts;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdZero;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdZeroNull;

abstract class ContractModelIdZero extends ContractModelId
{
    protected const CLASS_MODEL_REQUIRED = ModelIdZero::class;
    protected const CLASS_MODEL_NULLABLE = ModelIdZeroNull::class;

    protected ?int $minimumValueForModelId = 0;
}
