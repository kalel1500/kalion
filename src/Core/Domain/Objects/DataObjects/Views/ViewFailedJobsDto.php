<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Views;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\FailedJobCollection;

final class ViewFailedJobsDto extends AbstractDataTransferObject
{
    public function __construct(public readonly FailedJobCollection $jobs)
    {
    }
}
