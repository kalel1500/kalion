<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Services;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\SubRelationDataDo;

final class Relation
{
    public static function getNextRelation(string|array|null $with, bool|string|null $isFull, ?string $relationName): SubRelationDataDo
    {
        if (is_null($with)) return SubRelationDataDo::fromArray([null, null]);
        if (is_null($relationName)) return SubRelationDataDo::fromArray([$with, $isFull]);

        $with = is_array($with) ? $with : [$with];
        $newWith = null;
        $newIsFull = null;
        foreach ($with as $key => $rel) {

            if (is_string($key)) {
                [$key, $isFull] = get_info_from_relation_with_flag($key, $isFull);

                if ($key === $relationName) {
                    $newWith = $rel;
                    $newIsFull = $isFull;
                    break;
                }
            } else {
                $arrayRels = explode('.', $rel);
                $firstRel = $arrayRels[0];
                [$firstRel, $isFull] = get_info_from_relation_with_flag($firstRel, $isFull);

                if ($firstRel === $relationName) {
                    unset($arrayRels[0]);
                    $newWith = implode('.', $arrayRels);
                    $newIsFull = $isFull;
                    break;
                }
            }
        }
        $newWith = (empty($newWith)) ? null : $newWith;
        return SubRelationDataDo::fromArray([$newWith, $newIsFull]);
    }
}
