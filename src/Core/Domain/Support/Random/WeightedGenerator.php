<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Random;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionRuntimeException;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;

class WeightedGenerator
{
    /**
     * Maximum allowed number of values in the range ($maxValue - $minValue + 1).
     * Prevents memory overflows from uncontrolled calls to range().
     */
    public const MAX_RANGE_SIZE = 100_000;

    /**
     * Final normalized weights.
     *
     * @var array<int, float>
     */
    private array $weights = [];

    /**
     * Available numbers.
     *
     * @var list<int>
     */
    private array $numbers = [];

    /**
     * Cumulative limits.
     *
     * @var list<float>
     */
    private array $limits = [];

    /**
     * Sum of all weights.
     */
    private float $totalWeight = 0.0;

    public function __construct(
        private readonly int   $quantity,
        private readonly int   $minValue,
        private readonly int   $maxValue,
        private readonly array $customWeights = [],
    )
    {
        $this->validate();
    }

    /**
     * Generate weighted random numbers.
     */
    public function generate(): array
    {
        $this->normalizeWeights();

        $this->buildDistribution();

        $results = [];

        for ($i = 0; $i < $this->quantity; $i++) {
            $results[] = $this->pick();
        }

        return $results;
    }

    /**
     * Validate constructor arguments.
     */
    private function validate(): void
    {
        if ($this->minValue > $this->maxValue) {
            throw new InvalidValueException(
                __('k::error.min_value_cant_be_greater_than_max')
            );
        }

        if ($this->quantity <= 0) {
            throw new InvalidValueException(
                __('k::error.amount_must_be_greater_than_number', [
                    'number' => 0,
                ])
            );
        }

        $rangeSize = ($this->maxValue - $this->minValue) + 1;
        if ($rangeSize > self::MAX_RANGE_SIZE) {
            throw new InvalidValueException(__('k::error.range_exceeds_maximum_allowed', ['max' => self::MAX_RANGE_SIZE]));
        }

        foreach ($this->customWeights as $number => $weight) {
            if ((int)$number < $this->minValue || (int)$number > $this->maxValue) {
                throw new InvalidValueException(__('k::error.weight_key_out_of_range', ['number' => $number, 'min'    => $this->minValue, 'max'    => $this->maxValue,]));
            }
        }

        $totalCustomWeight = array_sum($this->customWeights);

        if ($totalCustomWeight > 100) {
            throw new InvalidValueException(
                __('k::error.sum_of_probabilities_cant_be_greater_than_100')
            );
        }
    }

    /**
     * Complete the weight map so every number in the range has a weight.
     *
     * Any value without an explicit weight receives an equal share of the
     * remaining probability.
     */
    private function normalizeWeights(): void
    {
        $this->weights = $this->customWeights;

        $remainingWeight = 100 - array_sum($this->customWeights);

        $remainingNumbers = array_diff(
            range($this->minValue, $this->maxValue),
            array_keys($this->customWeights),
        );

        if ($remainingNumbers !== []) {
            $weightPerNumber = $remainingWeight > 0
                ? $remainingWeight / count($remainingNumbers)
                : 0;

            foreach ($remainingNumbers as $number) {
                $this->weights[$number] = $weightPerNumber;
            }
        }

        // Values with zero probability can never be selected.
        $this->weights = array_filter(
            $this->weights,
            static fn(float|int $weight): bool => $weight > 0,
        );

        if ($this->weights === []) {
            throw new KalionRuntimeException(
                __('k::error.generated_distribution_empty')
            );
        }

        // Keep the distribution ordered by value.
        ksort($this->weights);
    }

    /**
     * Builds the cumulative distribution used for weighted selection.
     *
     * Example:
     *
     * Weights:
     * [
     *     0 => 50,
     *     20 => 20,
     *     100 => 30,
     * ]
     *
     * Result:
     *
     * Numbers:
     * [
     *     0,
     *     20,
     *     100,
     * ]
     *
     * Limits:
     * [
     *     50,
     *     70,
     *     100,
     * ]
     */
    private function buildDistribution(): void
    {
        foreach ($this->weights as $number => $weight) {
            $this->numbers[]   = (int)$number;
            $this->totalWeight += $weight;
            $this->limits[]    = $this->totalWeight;
        }
    }

    /**
     * Pick a single random value from the distribution.
     */
    private function pick(): int
    {
        $randomWeight = (random_int(0, PHP_INT_MAX - 1) / PHP_INT_MAX) * $this->totalWeight;

        $index = $this->binarySearch($randomWeight);

        return $this->numbers[$index];
    }

    /**
     * Finds the first cumulative limit greater than or equal to the target.
     *
     * Uses binary search because limits are always sorted.
     */
    private function binarySearch(float $target): int
    {
        $low  = 0;
        $high = count($this->limits) - 1;

        while ($low < $high) {
            $middle = intdiv($low + $high, 2);

            if ($target <= $this->limits[$middle]) {
                $high = $middle;
            } else {
                $low = $middle + 1;
            }
        }

        return $low;
    }
}
