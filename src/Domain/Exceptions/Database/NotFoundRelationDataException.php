<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\BasicException;

final class NotFoundRelationDataException extends BasicException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public function __construct(string $relation = '')
    {
        parent::__construct(sprintf('La entidad no contiene datos de la relación [%s]', $relation));
    }
}
