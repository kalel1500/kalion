<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\InferenceProblem;

abstract class CollectionBase
{
    private array $items;

    public function __construct($data)
    {
        $this->items = $data;
    }

    public function values(): array
    {
        return $this->items;
    }

    /**
     * @template T of CollectionBase
     *
     * @param class-string<T> $collectionClass
     * @return T
     */
    public function toCollection(string $collectionClass)
    {
        return $collectionClass::fromArray($this->toArray());
    }

    /**
     * @param string $field
     * @return CollectionAny
     */
    public function pluck(string $field)
    {
        $data = [];
        //...
        return new CollectionAny($data);
    }

    public function toArray(): array
    {
        $data = [];
        //...
        return $data;
    }

}
