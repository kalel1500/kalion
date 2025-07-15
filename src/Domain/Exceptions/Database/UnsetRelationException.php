<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

final class UnsetRelationException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function fromRelation(string $relation = '', string $entity = ''): static
    {
        return new static(sprintf('Call to relation [%s] that was not set when creating Entity [%s]', $relation, $entity));
    }
}
