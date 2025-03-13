<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\FailedJobCollection;

final class GetAllFailedJobsUseCase
{
    public function __construct(private JobRepositoryContract $repository)
    {
    }

    public function __invoke(): FailedJobCollection
    {
        return $this->repository->allFailed();
    }
}
