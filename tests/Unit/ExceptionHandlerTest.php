<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use JsonException;
use Mockery;
use ReflectionMethod;
use RuntimeException;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Exceptions\ExceptionHandler;
use Thehouseofel\Kalion\Tests\TestCase;

class ExceptionHandlerTest extends TestCase
{
    public function test_render_json_preserves_serializable_response_data(): void
    {
        $context = new ExceptionContextDto(
            e   : new RuntimeException('Public error'),
            data: ['reason' => 'expected_failure'],
        );

        $response = $this->renderJson($context);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame([
            'success' => false,
            'message' => 'Server Error',
            'data'    => ['reason' => 'expected_failure'],
        ], $response->getData(true));
    }

    public function test_render_json_returns_safe_fallback_for_non_serializable_response_data(): void
    {
        config()->set('app.debug', true);
        Log::spy();

        $context = new ExceptionContextDto(
            e   : new RuntimeException('Public error'),
            data: ['invalid_utf8' => "\xB1\x31"],
        );

        $response = $this->renderJson($context);
        $content  = $response->getData(true);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($content['success']);
        $this->assertNull($content['data']);
        $this->assertStringContainsString('could not be serialized', $content['message']);

        Log::shouldHaveReceived('error')
            ->once()
            ->with(
                'The exception response data could not be serialized.',
                Mockery::on(fn(array $logContext): bool =>
                    $logContext['exception'] instanceof JsonException
                    && is_string($logContext['response_data'] ?? null)
                    && str_contains($logContext['response_data'], 'invalid_utf8')
                ),
            );
    }

    public function test_render_json_does_not_expose_serialization_details_in_production(): void
    {
        config()->set('app.debug', false);
        Log::spy();

        $context = new ExceptionContextDto(
            e   : new RuntimeException('Sensitive error'),
            data: ['secret' => "\xB1\x31"],
        );

        $response = $this->renderJson($context);

        $this->assertSame([
            'success' => false,
            'message' => 'Server Error',
            'data'    => null,
        ], $response->getData(true));

        Log::shouldHaveReceived('error')
            ->once()
            ->with(
                'The exception response data could not be serialized.',
                Mockery::on(fn(array $logContext): bool =>
                    $logContext['exception'] instanceof JsonException
                    && ! array_key_exists('response_data', $logContext)
                ),
            );
    }

    private function renderJson(ExceptionContextDto $context): JsonResponse
    {
        $method = new ReflectionMethod(ExceptionHandler::class, 'renderJson');
        $method->setAccessible(true);

        return $method->invoke(null, $context);
    }
}


