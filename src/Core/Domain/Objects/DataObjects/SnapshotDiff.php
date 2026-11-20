<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use DateTimeInterface;

class SnapshotDiff
{
    /**
     * @param array<string, mixed> $old
     * @param array<string, mixed> $new
     */
    public function __construct(
        public readonly array $old,
        public readonly array $new,
    ) {
    }

    public static function between(array $oldSnapshot, array $newSnapshot): self
    {
        $old = [];
        $new = [];

        foreach ($newSnapshot as $key => $newValue) {
            $oldValue = $oldSnapshot[$key] ?? null;

            if (! self::areEqual($oldValue, $newValue)) {
                $old[$key] = $oldValue;
                $new[$key] = $newValue;
            }
        }

        return new self($old, $new);
    }

    /**
     * Dates (e.g. the `CarbonImmutable` of the date Value Objects) are compared as instants.
     */
    private static function areEqual(mixed $oldValue, mixed $newValue): bool
    {
        if ($oldValue instanceof DateTimeInterface && $newValue instanceof DateTimeInterface) {
            return $oldValue == $newValue;
        }

        return $oldValue === $newValue;
    }

    public function hasChanges(): bool
    {
        return $this->new !== [];
    }

    public function hasChanged(string $field): bool
    {
        return array_key_exists($field, $this->new);
    }

    /**
     * @return string[]
     */
    public function changedFields(): array
    {
        return array_keys($this->new);
    }
}
