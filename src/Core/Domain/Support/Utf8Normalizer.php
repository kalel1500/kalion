<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

final class Utf8Normalizer
{
    private const CANDIDATE_ENCODINGS = [
        'Windows-1252',
        'ISO-8859-1',
    ];

    /**
     * Converts invalid UTF-8 strings from an explicit or automatically
     * detected source encoding while preserving non-string values and arrays.
     */
    public static function normalize(mixed $value, ?string $fromEncoding = null): mixed
    {
        if (is_array($value)) {
            $normalized = [];

            foreach ($value as $key => $item) {
                $normalizedKey = is_string($key)
                    ? self::normalizeString($key, $fromEncoding)
                    : $key;

                $normalized[$normalizedKey] = self::normalize($item, $fromEncoding);
            }

            return $normalized;
        }

        return is_string($value)
            ? self::normalizeString($value, $fromEncoding)
            : $value;
    }

    private static function normalizeString(string $value, ?string $fromEncoding): string
    {
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $detectedEncoding = $fromEncoding
            ?? mb_detect_encoding($value, self::CANDIDATE_ENCODINGS, true);

        return $detectedEncoding === false
            ? mb_scrub($value, 'UTF-8')
            : mb_convert_encoding($value, 'UTF-8', $detectedEncoding);
    }
}
