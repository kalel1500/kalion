<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionLogicException;

final class FeatureUnavailableException extends KalionLogicException
{
    const STATUS_CODE = 500;

    public static function default(): static
    {
        return new static(__('k::error.feature_unavailable'));
    }
}
