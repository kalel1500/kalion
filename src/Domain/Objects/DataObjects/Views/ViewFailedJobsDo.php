<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Views;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\FailedJobCollection;

final class ViewFailedJobsDo extends ContractDataObject
{
    public function __construct(public readonly FailedJobCollection $jobs)
    {
    }
}
