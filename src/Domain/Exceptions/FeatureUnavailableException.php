<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\BasicException;

final class FeatureUnavailableException extends BasicException
{
    const STATUS_CODE = 500;

    public function __construct()
    {
        parent::__construct(__('h::error.feature_unavailable'));
    }
}
