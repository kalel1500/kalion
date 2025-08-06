<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions\Database;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionLogicException;

final class EntityRelationException extends KalionLogicException
{
    const STATUS_CODE = 500; // HTTP_INTERNAL_SERVER_ERROR

    public static function cannotDeleteDueToRelation(string $model = '', string $relation = ''): static
    {
        return new static(__('k::database.record_is_used_in_relation', ['model' => $model, 'relation' => $relation])); // TODO Canals - 409
    }

    public static function relationDataNotFound(string $entity = '', string $relation = ''): static
    {
        return new static("No hay datos en la relación [$relation] de la entidad [$entity]");
    }

    public static function relationNotLoadedInEloquentResult(string $relation = '', string $entity = ''): static
    {
        return new static("The relation '{$relation}' was not loaded in the Eloquent query result for entity '{$entity}'. Make sure to include it in the 'with()' clause.");
    }

    public static function relationNotSetInEntitySetup(string $relation = '', string $entity = ''): static
    {
        return new static("The relation '{$relation}' was not set when creating the entity '{$entity}'. Ensure it is properly initialized in the 'fromArray()' method.");
    }
}
