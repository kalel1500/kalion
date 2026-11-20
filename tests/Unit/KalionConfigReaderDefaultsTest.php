<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Thehouseofel\Kalion\Core\Domain\Support\KalionConfigReader;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\KalionConfigManager;

class KalionConfigReaderDefaultsTest extends TestCase
{
    private static function readerDefaults(): array
    {
        return (new ReflectionClass(KalionConfigReader::class))->getConstant('DEFAULTS');
    }

    public function test_kalion_defaults_match_the_config_manager_defaults(): void
    {
        $managerDefaults = KalionConfigManager::getDefaults();

        foreach (self::readerDefaults() as $method => [$key, $default]) {
            if (! str_starts_with($key, 'kalion.')) {
                continue;
            }

            $this->assertArrayHasKey($key, $managerDefaults, "The key [$key] of KalionConfigReader::$method() does not exist in KalionConfigManager::\$defaults.");
            $this->assertSame($managerDefaults[$key], $default, "The default of KalionConfigReader::$method() [$key] does not match KalionConfigManager::\$defaults.");
        }
    }

    public function test_non_kalion_defaults_only_read_laravel_app_keys(): void
    {
        foreach (self::readerDefaults() as $method => [$key]) {
            $this->assertTrue(
                str_starts_with($key, 'kalion.') || str_starts_with($key, 'app.'),
                "KalionConfigReader::$method() reads [$key], which is neither a kalion.* nor an app.* key."
            );
        }
    }

    public function test_every_default_is_documented_with_its_return_type(): void
    {
        $docComment = (string)(new ReflectionClass(KalionConfigReader::class))->getDocComment();

        $types = ['integer' => 'int', 'boolean' => 'bool', 'string' => 'string', 'double' => 'float', 'array' => 'array'];

        foreach (self::readerDefaults() as $method => [, $default]) {
            $type = $types[gettype($default)] ?? 'mixed';

            $this->assertStringContainsString("@method $type $method()", $docComment, "Missing or wrong \"@method $type $method()\" annotation in KalionConfigReader.");
        }

        preg_match_all('/@method\s+\S+\s+(\w+)\(\)/', $docComment, $matches);
        $this->assertEqualsCanonicalizing(array_keys(self::readerDefaults()), $matches[1], 'KalionConfigReader has @method annotations without DEFAULTS entry.');
    }
}
