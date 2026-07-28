<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal;

use PhpToken;
use Thehouseofel\Kalion\Core\Domain\Support\Path;

/**
 * @internal This class is intended for internal package usage only.
 */
final class PhpClass
{
    public static function fromFile(string $filePath): ?string
    {
        $filePath = Path::normalize($filePath);

        if (! file_exists($filePath)) {
            return null;
        }

        if (($contents = file_get_contents($filePath)) === false) {
            return null;
        }

        $tokens = PhpToken::tokenize($contents);

        $namespace = null;

        foreach ($tokens as $index => $token) {
            if ($token->id === T_NAMESPACE) {
                $namespace = static::readNamespace($tokens, $index);
                continue;
            }

            if ($token->id === T_CLASS) {
                // Evita falsos positivos como "new class extends Foo" (clase anónima)
                $previous = static::previousMeaningfulToken($tokens, $index);
                if ($previous?->text === 'new') {
                    continue;
                }

                $className = static::nextMeaningfulToken($tokens, $index)?->text;

                if ($className === null) {
                    continue;
                }

                return $namespace ? "{$namespace}\\{$className}" : $className;
            }
        }

        return null;
    }

    /**
     * @param PhpToken[] $tokens
     */
    private static function readNamespace(array $tokens, int $namespaceIndex): string
    {
        $namespace = '';

        for ($i = $namespaceIndex + 1; $i < count($tokens); $i++) {
            $token = $tokens[$i];

            if ($token->text === ';' || $token->text === '{') {
                break;
            }

            if (! $token->isIgnorable()) {
                $namespace .= $token->text;
            }
        }

        return trim($namespace);
    }

    /**
     * @param PhpToken[] $tokens
     */
    private static function previousMeaningfulToken(array $tokens, int $index): ?PhpToken
    {
        for ($i = $index - 1; $i >= 0; $i--) {
            if (! $tokens[$i]->isIgnorable()) {
                return $tokens[$i];
            }
        }

        return null;
    }

    /**
     * @param PhpToken[] $tokens
     */
    private static function nextMeaningfulToken(array $tokens, int $index): ?PhpToken
    {
        for ($i = $index + 1; $i < count($tokens); $i++) {
            if (! $tokens[$i]->isIgnorable()) {
                return $tokens[$i];
            }
        }

        return null;
    }
}
