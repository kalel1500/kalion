<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Jobs\Application;

use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\JobRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\FailedJobCollection;

final class GetAllFailedJobsUseCase
{
    public function __construct(private JobRepository $repository)
    {
    }

    public function __invoke(): FailedJobCollection
    {
        return $this->repository->allFailed();
    }
}
