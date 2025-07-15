<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionRuntimeException;

final class HasRelationException extends KalionRuntimeException
{
    const STATUS_CODE = 409; // HTTP_CONFLICT;

    public static function fromModel(string $model, string $relation): static
    {
        return new static(__('k::database.record_is_used_in_relation', ['model' => $model, 'relation' => $relation]));
    }
}
