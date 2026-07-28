<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use Illuminate\Support\Traits\Macroable;
use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionRuntimeException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;

class Random
{
    use Macroable;

    /**
     * Generates random integers using weighted probabilities.
     *
     * Any number without an explicit weight automatically receives an equal
     * share of the remaining probability.
     *
     * Example:
     * ```
     * Random::weighted(
     *     quantity: 10,
     *     minValue: 0,
     *     maxValue: 5,
     *     customWeights: [
     *         0 => 40,
     *         5 => 20,
     *     ],
     * );
     * ```
     */
    public static function weighted(
        int $quantity,
        int $minValue,
        int $maxValue,
        array $customWeights = [],
    ): array {
        return (new WeightedGenerator(
            quantity: $quantity,
            minValue: $minValue,
            maxValue: $maxValue,
            customWeights: $customWeights,
        ))->generate();
    }
}
