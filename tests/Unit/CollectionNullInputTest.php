<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionBase;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Collections\CollectionStrings;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDtoCollection;

class CollectionNullInputTest extends TestCase
{
    public static function collectionClasses(): array
    {
        return [
            [CollectionAny::class],
            [ExampleDtoCollection::class],
            [PostCollection::class],
            [CollectionStrings::class],
        ];
    }

    /** @param class-string<AbstractCollectionBase> $collectionClass */
    #[DataProvider('collectionClasses')]
    public function test_collections_build_empty_instance_from_null(string $collectionClass): void
    {
        $collection = $collectionClass::fromArray(null);

        $this->assertInstanceOf($collectionClass, $collection);
        $this->assertCount(0, $collection);
    }

    public function test_resolvable_collections_build_empty_instance_from_null(): void
    {
        $this->assertCount(0, ExampleDtoCollection::resolveFromArray(null));
        $this->assertCount(0, PostCollection::resolveFromArray(null));
    }

    /** @param class-string<AbstractCollectionBase> $collectionClass */
    #[DataProvider('collectionClasses')]
    public function test_collections_still_build_empty_instance_from_empty_array(string $collectionClass): void
    {
        $collection = $collectionClass::fromArray([]);

        $this->assertInstanceOf($collectionClass, $collection);
        $this->assertCount(0, $collection);
    }

    public function test_non_collection_objects_keep_nullable_behavior(): void
    {
        $this->assertNull(ExampleDto::fromArray(null));
        $this->assertNull(ExampleDto::resolveFromArray(null));
        $this->assertNull(PostEntity::fromArray(null));
        $this->assertNull(PostEntity::resolveFromArray(null));
    }
}




