<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\FailedJobCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\JobCollection;

interface JobRepository
{
    public function allExceptProcessing(): JobCollection;

    public function deleteAllExceptThoseNotInProcess(): void;

    public function allFailed(): FailedJobCollection;
}
