<?php

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDto;
use Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects\ExampleDtoCollection;

class ObjectsTest extends TestCase
{
    public static function getDto(): array
    {
        return [
            [new ExampleDtoCollection(
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue, ModelString::new('aa')),
                new ExampleDto('aaa', 'bbb', 3, CheckableProcessVo::queue, ModelString::new('aa')),
            )],
        ];
    }

    #[DataProvider('getDto')]
    public function test_dto_pluck_with_backed_enum(ExampleDtoCollection $dto)
    {
        $firstCheck = $dto->pluck('enum')->first();
        $this->assertEquals(CheckableProcessVo::queue, $firstCheck);

        $firstCheck = $dto->pluckValue('enum')->first();
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
}
