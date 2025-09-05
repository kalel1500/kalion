<?php

namespace Thehouseofel\Kalion\Domain\Traits;

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
