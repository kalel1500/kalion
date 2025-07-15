<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

final class NotFoundRelationDefinitionException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function fromRelation(string $relation = '', string $entity = ''): static
    {
        return new static(sprintf('Call to undefined relationship [%s] on repository data [%s]', $relation, $entity));
    }
}
