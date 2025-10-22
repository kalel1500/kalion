<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\KalionHttpException;

class AbortException extends KalionHttpException
{
    const SHOULD_RENDER_TRACE = true;
}
