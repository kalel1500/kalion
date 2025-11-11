<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepository;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\FailedJobCollection;

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
