<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Services;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\SubRelationDataDto;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class Relation
{
    public static function getNextRelation(string|array|null $with, bool|string|null $isFull, ?string $relationName): SubRelationDataDto
    {
        if (is_null($with)) return SubRelationDataDto::fromArray([null, null]);
        if (is_null($relationName)) return SubRelationDataDto::fromArray([$with, $isFull]);

        $with = is_array($with) ? $with : [$with];
        $newWith = null;
        $newIsFull = null;
        foreach ($with as $key => $rel) {

            if (is_string($key)) {
                [$key, $isFull] = Relation::getInfoFromRelationWithFlag($key, $isFull);

                if ($key === $relationName) {
                    $newWith = $rel;
                    $newIsFull = $isFull;
                    break;
                }
            } else {
                $arrayRels = explode('.', $rel);
                $firstRel = $arrayRels[0];
                [$firstRel, $isFull] = Relation::getInfoFromRelationWithFlag($firstRel, $isFull);

                if ($firstRel === $relationName) {
                    unset($arrayRels[0]);
                    $newWith = implode('.', $arrayRels);
                    $newIsFull = $isFull;
                    break;
                }
            }
        }
        $newWith = (empty($newWith)) ? null : $newWith;
        return SubRelationDataDto::fromArray([$newWith, $newIsFull]);
    }

    /**
     * @param string $relation
     * @param bool|string|null $isFull
     * @return array{string, bool|string|null}
     */
    public static function getInfoFromRelationWithFlag(string $relation, bool|string|null $isFull = null): array
    {
        if (str_contains($relation, ':')) {
            [$relation, $flag] = explode(':', $relation);
            $isFull = $flag === 'f' ? true : ($flag === 's' ? false : $flag);
        }
        return [$relation, $isFull];
    }
}
