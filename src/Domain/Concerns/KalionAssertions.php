<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Concerns;

use PHPUnit\Framework\Assert;

trait KalionAssertions
{
    /**
     * Assert that an array has a given structure.
     *
     * @param array $structure The structure to validate against.
     * @param array $array The array to be validated.
     * @return void
     */
    public function assertArrayStructure(array $structure, array $array): void
    {
        foreach ($structure as $key => $value) {
            // Handle collections represented by the asterisk.
            if (is_array($value) && $key === '*') {
                Assert::assertIsArray($array);

                // Recursively validate each item in the collection.
                foreach ($array as $item) {
                    $this->assertArrayStructure($structure['*'], $item);
                }
                // Handle nested arrays.
            } elseif (is_array($value)) {
                Assert::assertArrayHasKey($key, $array);
                Assert::assertIsArray($array[$key]);

                // Recursively validate the nested array.
                $this->assertArrayStructure($value, $array[$key]);
                // Handle simple keys (strings).
            } else {
                Assert::assertArrayHasKey($value, $array);
            }
        }
    }
}
