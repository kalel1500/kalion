<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

final class NotFoundRelationDataException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function fromRelation(string $relation = ''): static
    {
        return new static(sprintf('La entidad no contiene datos de la relación [%s]', $relation));
    }
}
