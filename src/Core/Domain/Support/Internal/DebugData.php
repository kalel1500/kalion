<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Internal;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Throwable;

/**
 * @internal This class is intended for internal package usage only.
 */
final class DebugData
{
    public static function normalize(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            $normalized[self::normalizeKey($key)] = self::dump($value);
        }

        return $normalized;
    }

    public static function dump(mixed $value): string
    {
        try {
            $cloner = new VarCloner();
            $cloner->setMaxItems(1_000);
            $cloner->setMaxString(10_000);

            $dumper = new CliDumper();
            $dumper->setColors(false);

            return $dumper->dump($cloner->cloneVar($value), true);
        } catch (Throwable) {
            return sprintf('[Unable to dump value of type %s]', get_debug_type($value));
        }
    }

    private static function normalizeKey(int|string $key): string
    {
        if (is_int($key)) {
            return (string)$key;
        }

        return preg_match('//u', $key) === 1 ? $key : bin2hex($key);
    }
}


