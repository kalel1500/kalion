<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Unit;

use PHPUnit\Framework\TestCase;
use stdClass;
use Thehouseofel\Kalion\Core\Domain\Support\Utf8Normalizer;

class Utf8NormalizerTest extends TestCase
{
    public function test_it_preserves_valid_utf8_and_non_string_values(): void
    {
        $object = new stdClass();
        $value  = [
            'text'   => 'Conexión correcta',
            'int'    => 10,
            'float'  => 1.5,
            'bool'   => true,
            'null'   => null,
            'object' => $object,
        ];

        $normalized = Utf8Normalizer::normalize($value);

        $this->assertSame($value, $normalized);
        $this->assertSame($object, $normalized['object']);
    }

    public function test_it_automatically_detects_nested_windows_1252_strings_and_keys(): void
    {
        $invalidKey   = mb_convert_encoding('descripción', 'Windows-1252', 'UTF-8');
        $invalidValue = mb_convert_encoding('Conexión con coste de 10 €', 'Windows-1252', 'UTF-8');

        $normalized = Utf8Normalizer::normalize([
            'headers' => [
                $invalidKey => [$invalidValue],
            ],
        ]);

        $this->assertSame([
            'headers' => [
                'descripción' => ['Conexión con coste de 10 €'],
            ],
        ], $normalized);
    }

    public function test_it_accepts_windows_1252_as_explicit_source_encoding(): void
    {
        $this->assertSame(
            'conexión',
            Utf8Normalizer::normalize("conexi\xF3n", 'Windows-1252'),
        );
        $this->assertSame(
            'Error €',
            Utf8Normalizer::normalize("Error \x80", 'Windows-1252'),
        );
    }

    public function test_it_accepts_a_custom_source_encoding(): void
    {
        $iso88591 = mb_convert_encoding('Petición inválida', 'ISO-8859-1', 'UTF-8');

        $this->assertSame(
            'Petición inválida',
            Utf8Normalizer::normalize($iso88591, 'ISO-8859-1'),
        );
    }
}
