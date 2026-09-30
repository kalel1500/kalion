<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use JsonException;
use PHPUnit\Framework\Attributes\DataProvider;
use Thehouseofel\Kalion\Core\Domain\Exceptions\InvalidValueException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections\TabulatorDataCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections\TabulatorFilterCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections\TabulatorSortCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\TabulatorFilterDto;
use Thehouseofel\Kalion\Tests\TestCase;

class TabulatorDataCollectionTest extends TestCase
{
    public static function tabulatorCollectionClasses(): array
    {
        return [
            [TabulatorFilterCollection::class],
            [TabulatorSortCollection::class],
        ];
    }

    /** @param class-string<TabulatorDataCollection> $collectionClass */
    #[DataProvider('tabulatorCollectionClasses')]
    public function test_from_tabulator_returns_empty_collection_for_null(string $collectionClass): void
    {
        $collection = $collectionClass::fromTabulator(null);

        $this->assertInstanceOf($collectionClass, $collection);
        $this->assertCount(0, $collection);
    }

    public function test_from_tabulator_returns_empty_collection_for_empty_array(): void
    {
        $collection = TabulatorFilterCollection::fromTabulator([]);

        $this->assertCount(0, $collection);
    }

    public function test_from_tabulator_hydrates_dtos_from_array(): void
    {
        $collection = TabulatorFilterCollection::fromTabulator([
            ['field' => 'email', 'type' => 'like', 'value' => '@example.com'],
        ]);

        $this->assertInstanceOf(TabulatorFilterDto::class, $collection->first());
        $this->assertSame('email', $collection->first()->field);
    }

    public function test_from_tabulator_decodes_url_encoded_json(): void
    {
        $collection = TabulatorFilterCollection::fromTabulator(urlencode(json_encode([
            ['field' => 'name', 'type' => '=', 'value' => 'María López'],
        ], JSON_THROW_ON_ERROR)));

        $this->assertSame([
            ['field' => 'name', 'type' => '=', 'value' => 'María López'],
        ], $collection->toArray());
    }

    public function test_to_tabulator_round_trip(): void
    {
        $original = TabulatorFilterCollection::fromArray([
            ['field' => 'status', 'type' => '=', 'value' => 'active'],
        ]);

        $result = TabulatorFilterCollection::fromTabulator($original->toTabulator());

        $this->assertEquals($original, $result);
    }

    public function test_from_tabulator_throws_for_malformed_json(): void
    {
        try {
            TabulatorFilterCollection::fromTabulator('%5Binvalid-json%5D');
            $this->fail('An InvalidValueException was not thrown.');
        } catch (InvalidValueException $exception) {
            $this->assertInstanceOf(JsonException::class, $exception->getPrevious());
            $this->assertSame('The Tabulator value must contain valid JSON.', $exception->getMessage());
        }
    }

    public function test_from_tabulator_throws_when_json_root_is_not_an_array(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('must decode to an array');

        TabulatorFilterCollection::fromTabulator(urlencode('null'));
    }
}
