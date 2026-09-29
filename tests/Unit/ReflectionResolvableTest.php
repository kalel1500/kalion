<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Illuminate\Container\Container;
use JsonException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Thehouseofel\Kalion\Core\Domain\Exceptions\KalionReflectionException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes\DisableReflection;
use Thehouseofel\Kalion\Core\Domain\Support\Internal\DebugData;

class ReflectionResolvableTest extends TestCase
{
    private Container $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousContainer = Container::getInstance();

        $container = new class extends Container {
            public function hasDebugModeEnabled(): bool
            {
                return true;
            }
        };
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

        try {
            $dto->toArray();
            $this->fail('A serialization exception was expected.');
        } catch (KalionReflectionException $exception) {
            $context   = $exception->getExceptionContext();
            $debugData = $context->debugData;

            $this->assertStringContainsString(
                'Failed to serialize ' . JsonSerializationDto::class,
                $exception->getMessage(),
            );
            $this->assertInstanceOf(JsonException::class, $exception->getPrevious());
            $this->assertStringContainsString(JsonSerializationDto::class, $debugData['serialization_class']);
            $this->assertStringContainsString('data', $debugData['serialization_target']);
            $this->assertSame(['kalion_debug' => $debugData], $exception->context());
            $this->assertArrayNotHasKey('debug_data', $context->toArray(false));
            $this->assertSame($debugData, $context->toArray()['debug_data']);
            $this->assertIsString(json_encode($context->toArray(), JSON_THROW_ON_ERROR));
        }
    }

    public function test_debug_data_safely_dumps_circular_references(): void
    {
        $recursive         = [];
        $recursive['self'] = &$recursive;

        $debugData = DebugData::normalize(['recursive' => $recursive]);

        $this->assertStringContainsString('&1', $debugData['recursive']);
        $this->assertIsString(json_encode($debugData, JSON_THROW_ON_ERROR));
    }
}

#[DisableReflection(useJsonSerialization: true)]
final class JsonSerializationDto extends AbstractDataTransferObject
{
    public function __construct(public mixed $data)
    {
    }
}



