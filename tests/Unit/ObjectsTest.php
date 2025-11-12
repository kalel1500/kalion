<?php

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDtoCollection;

class ObjectsTest extends TestCase
{
    public static function getDto(): array
    {
        return [
            [new ExampleDtoCollection(
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue, StringVo::from('aa')),
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue, StringVo::from('aa')),
            )],
        ];
    }

    #[DataProvider('getDto')]
    public function test_dto_pluck_with_backed_enum(ExampleDtoCollection $dto)
    {
        $firstCheck = $dto->pluck('enum')->first();
        $this->assertEquals(CheckableProcessVo::queue->value, $firstCheck);

    }

    #[DataProvider('getDto')]
    public function test_dto_pluck_field_only_in_to_array(ExampleDtoCollection $dto)
    {
        $firstOnlyInToArray = $dto->pluck('only_in_to_array')->first();
        $this->assertEquals('text', $firstOnlyInToArray);
    }

    public function test_dto_to_array_with_null_values()
    {
        $dto = new ExampleDto('aaa', 'bbb', 3, null, null);
        $this->assertIsArray($dto->toArray());
    }

    public function test_dto_from_array_with_null_values()
    {
        $dto = ExampleDto::fromArray([
            'string1' => 'aaa',
            'string2' => 'bbb',
            'number' => 1,
            'enum' => null,
            'modelString' => null,
        ]);
        $this->assertInstanceOf(ExampleDto::class, $dto);
    }

    public static function getCollection(): array
    {
        $array = [
            'users' => [
                '1' => ['name' => 'Test',   'email' => 'test@example.com'],
                '2' => ['name' => 'Maria',  'email' => 'test1@example.com'],
                '3' => ['name' => 'Lola',   'email' => 'test2@example.com'],
                '4' => ['name' => 'Sergio', 'email' => 'test3@example.com'],
                '5' => ['name' => 'Ander',  'email' => 'test4@example.com'],
                '6' => ['name' => 'David',  'email' => 'test5@example.com'],
                '7' => ['name' => 'Jaime',  'email' => 'test6@example.com'],
                '8' => ['name' => 'Pablo',  'email' => 'test7@example.com'],
                '9' => ['name' => 'Oriol',  'email' => 'test8@example.com'],
                '10'=> ['name' => 'Lorena', 'email' => 'test9@example.com'],
            ],
            'phones' => [
                ['name' => 'iPhone 6',      'brand' => 'Apple',     'type' => 'phone'],
                ['name' => 'iPhone 5',      'brand' => 'Apple',     'type' => 'phone'],
                ['name' => 'Apple Watch',   'brand' => 'Apple',     'type' => 'watch'],
                ['name' => 'Galaxy S6',     'brand' => 'Samsung',   'type' => 'phone'],
                ['name' => 'Galaxy Gear',   'brand' => 'Samsung',   'type' => 'watch'],
            ],
            'products' => [
                ['product' => 'Desk',       'price' => 200],
                ['product' => 'Chair',      'price' => 80],
                ['product' => 'Bookcase',   'price' => 150],
                ['product' => 'Pencil',     'price' => 30],
                ['product' => 'Door',       'price' => 100],
            ],
        ];
        return [
            [CollectionAny::class, $array],
        ];
    }

    #[DataProvider('getCollection')]
    public function test_collection_filter($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['users']);
        $result = $data
            ->filter(function (array $value, $key) {
                return str_contains($value['name'], 'Test') || str_contains($value['name'], 'Lola');
            })
            ->all();
        $this->assertEquals([
            '1' => ['name' => 'Test', 'email' => 'test@example.com'],
            '3' => ['name' => 'Lola', 'email' => 'test2@example.com'],
        ], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_flatten($collection)
    {
        /** @var CollectionAny $collection */
        $data = new $collection(['name' => 'Taylor', 'languages' => ['PHP', 'JavaScript']]);
        $result = $data->flatten()->all();
        $this->assertEquals(['Taylor', 'PHP', 'JavaScript'], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_sort($collection)
    {
        /** @var CollectionAny $collection */
        $data = new $collection([5, 3, 1, 2, 4]);
        $result = $data->sort()->values()->all();
        $this->assertEquals([1, 2, 3, 4, 5], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_sort_by($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['users']);
        $result = $data->sortBy('name')->pluck('name')->take(3)->all();
        $this->assertEquals(['Ander', 'David', 'Jaime'], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_sort_desc($collection)
    {
        /** @var CollectionAny $collection */
        $data = new $collection([1, 2, 3, 4, 5]);
        $result = $data->sortDesc()->values()->all();
        $this->assertEquals([5, 4, 3, 2, 1], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_take($collection)
    {
        /** @var CollectionAny $collection */
        $data = new $collection([1, 2, 3, 4, 5]);
        $result = $data->take(3)->all();
        $this->assertEquals([1, 2, 3], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_unique($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data1 = new $collection([1, 1, 2, 2, 3, 4, 2]);
        $result1 = $data1->unique()->values()->all();
        $this->assertEquals([1, 2, 3, 4], $result1);

        $data2 = new $collection($data['phones']);
        $result2 = $data2->unique('brand')->values()->all();
        $this->assertEquals([
            ['name' => 'iPhone 6', 'brand' => 'Apple', 'type' => 'phone'],
            ['name' => 'Galaxy S6', 'brand' => 'Samsung', 'type' => 'phone'],
        ], $result2);
    }

    #[DataProvider('getCollection')]
    public function test_collection_where($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['products']);
        $result = $data
            ->where('price', 100)
            ->all();
        $this->assertEquals([
            '4' => ['product' => 'Door', 'price' => 100],
        ], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_where_in($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['products']);
        $result = $data
            ->whereIn('price', [80, 100])
            ->all();
        $this->assertEquals([
            '1' => ['product' => 'Chair','price' => 80],
            '4' => ['product' => 'Door', 'price' => 100],
        ], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_where_not_in($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['products']);
        $result = $data
            ->whereNotIn('price', [30, 150, 200])
            ->all();
        $this->assertEquals([
            '1' => ['product' => 'Chair','price' => 80],
            '4' => ['product' => 'Door', 'price' => 100],
        ], $result);
    }

    #[DataProvider('getCollection')]
    public function test_collection_map($collection, $data)
    {
        /** @var CollectionAny $collection */
        $data = new $collection($data['products']);
        $result = $data
            ->map(function (array $item, int $key) {
                return $item['product'] . 2 . $key;
            })
            ->all();
        $this->assertEquals([
            'Desk20',
            'Chair21',
            'Bookcase22',
            'Pencil23',
            'Door24',
        ], $result);
    }
}
