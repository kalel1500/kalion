<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionBase;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Collections\CollectionStrings;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\Collections\TagTypeCollection;
use Thehouseofel\Kalion\Tests\Support\Contexts\Blog\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDtoCollection;
use Thehouseofel\Kalion\Tests\TestCase;

class CollectionItemCallbacksTest extends TestCase
{
    private function tagTypes(string|array|null $with = null, bool|string|null $isFull = null): TagTypeCollection
    {
        return TagTypeCollection::fromArray([
            ['id' => 1, 'name' => 'Tecnología', 'code' => 'tech'],
            ['id' => 2, 'name' => 'Deportes', 'code' => 'sport'],
            ['id' => 3, 'name' => 'Música', 'code' => 'music'],
        ], $with, $isFull);
    }

    private function dtos(): ExampleDtoCollection
    {
        return new ExampleDtoCollection(
            new ExampleDto('aaa', 'x', 1, CheckableProcessVo::queue, StringVo::from('foo')),
            new ExampleDto('bbb', 'y', 2, null, StringVo::from('bar')),
            new ExampleDto('ccc', 'z', 3, null, StringVo::from('foo')),
        );
    }

    private function readProperty(object $object, string $property): mixed
    {
        return (new ReflectionProperty($object, $property))->getValue($object);
    }

    /* -----------------------------------------------------------------
     | filter / reject reciben los items originales y la clave
     | ----------------------------------------------------------------- */

    public function test_filter_receives_entity_instances_and_keys(): void
    {
        $received = [];

        $result = $this->tagTypes()->filter(function (TagTypeEntity $tagType, int $key) use (&$received) {
            $received[$key] = $tagType;
            return $tagType->code->value !== 'sport';
        });

        $this->assertSame([0, 1, 2], array_keys($received));
        $this->assertContainsOnlyInstancesOf(TagTypeEntity::class, $received);
        $this->assertInstanceOf(TagTypeCollection::class, $result);
        $this->assertSame([0, 2], $result->keys()->all());
    }

    public function test_reject_receives_entity_instances_and_keys(): void
    {
        $received = [];

        $result = $this->tagTypes()->reject(function (TagTypeEntity $tagType, int $key) use (&$received) {
            $received[$key] = $tagType;
            return $tagType->code->value === 'sport';
        });

        $this->assertSame([0, 1, 2], array_keys($received));
        $this->assertContainsOnlyInstancesOf(TagTypeEntity::class, $received);
        $this->assertInstanceOf(TagTypeCollection::class, $result);
        $this->assertSame([0, 2], $result->keys()->all());
    }

    public function test_filter_and_reject_receive_dto_instances(): void
    {
        $filtered = $this->dtos()->filter(fn(ExampleDto $dto, int $key) => $dto->modelString->value === 'foo');
        $rejected = $this->dtos()->reject(fn(ExampleDto $dto, int $key) => $dto->modelString->value === 'foo');

        $this->assertInstanceOf(ExampleDtoCollection::class, $filtered);
        $this->assertSame(['aaa', 'ccc'], $filtered->map(fn(ExampleDto $dto) => $dto->string1)->values()->all());
        $this->assertSame([1], $rejected->keys()->all());
    }

    public function test_filter_receives_value_object_instances(): void
    {
        $collection = CollectionStrings::fromArray(['a', 'bb', 'ccc']);

        $result = $collection->filter(fn(StringVo $vo) => strlen($vo->value) > 1);

        $this->assertInstanceOf(CollectionStrings::class, $result);
        $this->assertSame([1 => 'bb', 2 => 'ccc'], $result->toArray());
    }

    /* -----------------------------------------------------------------
     | Identidad de los items
     | ----------------------------------------------------------------- */

    public static function collectionsProvider(): array
    {
        return [
            'entity' => [fn(self $t) => $t->tagTypes()],
            'dto'    => [fn(self $t) => $t->dtos()],
            'vo'     => [fn() => CollectionStrings::fromArray(['a', 'b', 'c'])],
            'any'    => [fn() => new CollectionAny([(object)['a' => 1], (object)['a' => 2], (object)['a' => 3]])],
        ];
    }

    #[DataProvider('collectionsProvider')]
    public function test_item_identity_is_preserved(callable $factory): void
    {
        /** @var AbstractCollectionBase $collection */
        $collection = $factory($this);

        $this->assertSame($collection->first(), $collection->filter(fn() => true)->first());
        $this->assertSame($collection->first(), $collection->reject(fn() => false)->first());
        $this->assertSame($collection->last(), $collection->reverse()->first());
        $this->assertSame($collection->last(), $collection->skip(2)->first());
        $this->assertSame($collection->all()[1], $collection->slice(1, 1)->first());

        [$passed, $failed] = $collection->partition(fn($item, $key) => $key === 0);
        $this->assertSame($collection->first(), $passed->first());
        $this->assertSame($collection->last(), $failed->last());

        $this->assertInstanceOf($collection::class, $passed);
        $this->assertInstanceOf($collection::class, $failed);
    }

    public function test_filter_does_not_mutate_the_original_collection(): void
    {
        $collection = $this->tagTypes();

        $collection->filter(fn() => false);

        $this->assertCount(3, $collection);
    }

    /* -----------------------------------------------------------------
     | Relatable: se conservan with e isFull
     | ----------------------------------------------------------------- */

    public function test_relatable_entity_collection_keeps_with_and_is_full(): void
    {
        $collection = $this->tagTypes()->setWith('tags')->setIsFull(true);

        $results = [
            $collection->filter(fn() => true),
            $collection->reject(fn() => false),
            $collection->skip(1),
            $collection->slice(0, 2),
            $collection->reverse(),
            ...$collection->partition(fn($item, $key) => $key > 0),
        ];

        foreach ($results as $result) {
            $this->assertSame('tags', $this->readProperty($result, 'with'));
            $this->assertTrue($this->readProperty($result, 'isFull'));
        }
    }

    public function test_relatable_collection_any_keeps_with_and_is_full(): void
    {
        $collection = CollectionAny::fromArray([1, 2, 3], ['posts'], 'context');

        $result = $collection->filter(fn(int $item) => $item > 1);

        $this->assertSame([1 => 2, 2 => 3], $result->all());
        $this->assertSame(['posts'], $this->readProperty($result, 'with'));
        $this->assertSame('context', $this->readProperty($result, 'isFull'));
    }

    /* -----------------------------------------------------------------
     | skip / slice / reverse / partition
     | ----------------------------------------------------------------- */

    public function test_skip_preserves_keys_and_values_reindexes(): void
    {
        $result = $this->tagTypes()->skip(1);

        $this->assertInstanceOf(TagTypeCollection::class, $result);
        $this->assertCount(2, $result);
        $this->assertSame([1, 2], $result->keys()->all());
        $this->assertSame([0, 1], $result->values()->keys()->all());
    }

    public function test_slice_preserves_keys(): void
    {
        $collection = new CollectionAny(['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4]);

        $this->assertSame(['b' => 2, 'c' => 3], $collection->slice(1, 2)->all());
        $this->assertSame(['c' => 3, 'd' => 4], $collection->slice(2)->all());
    }

    public function test_reverse_preserves_keys(): void
    {
        $collection = new CollectionAny([1, 2, 3]);

        $this->assertSame([2 => 3, 1 => 2, 0 => 1], $collection->reverse()->all());
    }

    public function test_partition_splits_collection_preserving_keys(): void
    {
        [$passed, $failed] = $this->dtos()->partition(fn(ExampleDto $dto) => $dto->number % 2 === 1);

        $this->assertInstanceOf(ExampleDtoCollection::class, $passed);
        $this->assertInstanceOf(ExampleDtoCollection::class, $failed);
        $this->assertSame([0, 2], $passed->keys()->all());
        $this->assertSame([1], $failed->keys()->all());
    }

    /* -----------------------------------------------------------------
     | where sigue trabajando sobre arrays (props y campos de VOs)
     | ----------------------------------------------------------------- */

    public function test_where_filters_by_scalar_props(): void
    {
        $result = $this->dtos()->where('number', 2);

        $this->assertInstanceOf(ExampleDtoCollection::class, $result);
        $this->assertSame([1], $result->keys()->all());
        $this->assertSame('bbb', $result->first()->string1);
    }

    public function test_where_filters_by_value_object_fields(): void
    {
        $dtos = $this->dtos()->where('modelString', 'foo');
        $this->assertSame([0, 2], $dtos->keys()->all());
        $this->assertContainsOnlyInstancesOf(ExampleDto::class, $dtos->all());

        $tagTypes = $this->tagTypes()->where('code', 'sport');
        $this->assertInstanceOf(TagTypeCollection::class, $tagTypes);
        $this->assertSame([1], $tagTypes->keys()->all());
        $this->assertSame('Deportes', $tagTypes->first()->name->value);
    }

    /* -----------------------------------------------------------------
     | Métodos por clave: calculan sobre arrays pero devuelven los items originales
     | ----------------------------------------------------------------- */

    public function test_where_preserves_identity(): void
    {
        $tagTypes = $this->tagTypes();
        $result   = $tagTypes->where('code', 'sport');
        $this->assertSame($tagTypes[1], $result[1]);

        $dtos   = $this->dtos();
        $result = $dtos->where('modelString', 'foo');
        $this->assertSame($dtos[0], $result[0]);
        $this->assertSame($dtos[2], $result[2]);

        $this->assertSame($dtos[1], $dtos->firstWhere('number', 2));
        $this->assertSame([0, 1, 2], $dtos->whereNotNull('modelString')->keys()->all());
        $this->assertSame([0], $dtos->whereNotNull('enum')->keys()->all());
    }

    public function test_where_in_and_where_not_in_preserve_identity(): void
    {
        $tagTypes = $this->tagTypes();

        $in = $tagTypes->whereIn('code', ['tech', 'music']);
        $this->assertInstanceOf(TagTypeCollection::class, $in);
        $this->assertSame([0, 2], $in->keys()->all());
        $this->assertSame($tagTypes[0], $in[0]);
        $this->assertSame($tagTypes[2], $in[2]);

        $notIn = $tagTypes->whereNotIn('code', ['tech', 'music']);
        $this->assertSame([1], $notIn->keys()->all());
        $this->assertSame($tagTypes[1], $notIn[1]);
    }

    public function test_sort_by_field_keeps_order_and_identity(): void
    {
        $tagTypes = $this->tagTypes();

        // Ordena por el valor escalar del Value Object `name`: Deportes, Música, Tecnología
        $asc = $tagTypes->sortBy('name');
        $this->assertInstanceOf(TagTypeCollection::class, $asc);
        $this->assertSame([1, 2, 0], $asc->keys()->all());
        $this->assertSame([$tagTypes[1], $tagTypes[2], $tagTypes[0]], $asc->values()->all());

        $desc = $tagTypes->sortByDesc('name');
        $this->assertSame([0, 2, 1], $desc->keys()->all());
        $this->assertSame($tagTypes[0], $desc->first());
    }

    public function test_sort_by_callable_receives_items_and_keeps_order_and_identity(): void
    {
        $dtos = $this->dtos();

        $result = $dtos->sortBy(fn(ExampleDto $dto, int $key) => -$dto->number);

        $this->assertSame([2, 1, 0], $result->keys()->all());
        $this->assertSame([$dtos[2], $dtos[1], $dtos[0]], $result->values()->all());
    }

    public function test_sort_preserves_identity(): void
    {
        $dtos = $this->dtos();

        $byCallback = $dtos->sort(fn(ExampleDto $a, ExampleDto $b) => $b->number <=> $a->number);
        $this->assertSame([$dtos[2], $dtos[1], $dtos[0]], $byCallback->values()->all());

        $plain = new CollectionAny([3, 1, 2]);
        $this->assertSame([1 => 1, 2 => 2, 0 => 3], $plain->sort()->all());
    }

    public function test_values_reindexes_and_preserves_identity(): void
    {
        $tagTypes = $this->tagTypes()->setWith('tags')->setIsFull(true);
        $filtered = $tagTypes->filter(fn(TagTypeEntity $tagType) => $tagType->code->value !== 'tech');

        $values = $filtered->values();

        $this->assertInstanceOf(TagTypeCollection::class, $values);
        $this->assertSame([0, 1], $values->keys()->all());
        $this->assertSame($tagTypes[1], $values[0]);
        $this->assertSame($tagTypes[2], $values[1]);
        $this->assertSame('tags', $this->readProperty($values, 'with'));
        $this->assertTrue($this->readProperty($values, 'isFull'));
    }

    public function test_take_preserves_identity(): void
    {
        $tagTypes = $this->tagTypes();

        $this->assertSame([$tagTypes[0], $tagTypes[1]], $tagTypes->take(2)->all());
        $this->assertSame([2 => $tagTypes[2]], $tagTypes->take(-1)->all());
    }

    public function test_unique_preserves_identity(): void
    {
        $dtos = $this->dtos();

        $byField = $dtos->unique('modelString');
        $this->assertSame([0, 1], $byField->keys()->all());
        $this->assertSame($dtos[0], $byField[0]);

        $byCallable = $dtos->unique(fn(ExampleDto $dto) => $dto->modelString->value);
        $this->assertSame([0, 1], $byCallable->keys()->all());
        $this->assertSame($dtos[1], $byCallable[1]);
    }

    /* -----------------------------------------------------------------
     | Variantes con callable: reciben los items originales
     | ----------------------------------------------------------------- */

    public function test_every_with_callable_receives_items(): void
    {
        $dtos = $this->dtos();

        $this->assertTrue($dtos->every(fn(ExampleDto $dto, int $key) => $dto->number > 0));
        $this->assertFalse($dtos->every(fn(ExampleDto $dto) => $dto->modelString->value === 'foo'));
        $this->assertTrue($dtos->every('number', '>', 0));
    }

    public function test_group_by_callable_receives_items_and_groups_keep_identity(): void
    {
        $dtos = $this->dtos();

        $groups = $dtos->groupBy(fn(ExampleDto $dto, int $key) => $dto->modelString->value);

        $this->assertInstanceOf(CollectionAny::class, $groups);
        $this->assertSame(['foo', 'bar'], $groups->keys()->all());
        $this->assertInstanceOf(ExampleDtoCollection::class, $groups['foo']);
        $this->assertSame([$dtos[0], $dtos[2]], $groups['foo']->all());
        $this->assertSame([$dtos[1]], $groups['bar']->all());

        $byField = $dtos->groupBy('modelString');
        $this->assertInstanceOf(ExampleDtoCollection::class, $byField['foo']);
        $this->assertCount(2, $byField['foo']);
    }

    public function test_max_min_and_implode_with_callable_receive_items(): void
    {
        $dtos = $this->dtos();

        $this->assertSame(3, $dtos->max(fn(ExampleDto $dto) => $dto->number));
        $this->assertSame(1, $dtos->min(fn(ExampleDto $dto) => $dto->number));
        $this->assertSame('aaa-bbb-ccc', $dtos->implode(fn(ExampleDto $dto) => $dto->string1, '-'));

        $this->assertSame(3, $dtos->max('number'));
        $this->assertSame(1, $dtos->min('number'));
        $this->assertSame('aaa, bbb, ccc', $dtos->implode('string1', ', '));
    }
}



