<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

final class NotFoundRelationDefinitionException extends KalionRuntimeException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public function __construct(string $relation = '', string $entity = '')
    {
        parent::__construct(sprintf('Call to undefined relationship [%s] on repository data [%s]', $relation, $entity));
    }
}
