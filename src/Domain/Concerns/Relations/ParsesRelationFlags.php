<?php

namespace Thehouseofel\Kalion\Domain\Concerns\Relations;

trait ParsesRelationFlags
{
    protected function getInfoFromRelationWithFlag(string $relation): array
    {
        if (str_contains($relation, ':')) {
            [$name, $flag] = explode(':', $relation, 2);
            $flag = match (true) {
                $flag === 'f' => true,
                $flag === 's' => false,
                default       => $flag,
            };
            return [$name, $flag];
        }

        return [$relation, null];
    }
}
