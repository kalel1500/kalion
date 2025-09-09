<?php

namespace Thehouseofel\Kalion\Domain\Concerns\Relations;

trait ParsesRelationFlags
{
    protected function getInfoFromRelationWithFlag(string $relation): array
    {
        $isFull = null;
        if (str_contains($relation, ':')) {
            [$relation, $flag] = explode(':', $relation);
            $isFull = $flag === 'f' ? true : ($flag === 's' ? false : $flag);
        }
        return [$relation, $isFull];
    }
}
