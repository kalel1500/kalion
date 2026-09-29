<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use stdClass;
use Thehouseofel\Kalion\Core\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;

class ReflectionResolvableTest extends TestCase
{
    private Container $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousContainer = Container::getInstance();

        $container = new Container();
        $container->instance('translator', new class {
            public function get(string $key): string
            {
                return $key;
            }
        });
        Container::setInstance($container);
    }

    protected function tearDown(): void
    {
        Container::setInstance($this->previousContainer);

        parent::tearDown();
    }

    public function test_disabled_reflection_can_serialize_properties_to_an_array(): void
    {
        $nested        = new stdClass();
        $nested->value = 'serialized';
        $dto           = new JsonSerializationDto($nested);

        $this->assertSame([
            'data' => [
                'value' => 'serialized',
            ],
        ], $dto->toArray());
    }

    public function test_disabled_reflection_throws_when_json_serialization_fails(): void
    {
        $dto = new JsonSerializationDto("\xB1\x31");

        $this->expectException(KalionReflectionException::class);
        $this->expectExceptionMessage(
            'Failed to serialize ' . JsonSerializationDto::class . ' to an array using JSON serialization.'
        );

        $dto->toArray();
    }
}

#[DisableReflection(useJsonSerialization: true)]
final class JsonSerializationDto extends AbstractDataTransferObject
{
    public function __construct(public mixed $data)
    {
    }
}



