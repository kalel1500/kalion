<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\JobCollection;

final class GetAllJobsUseCase
{
    private $repository;

    public function __construct(JobRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(): JobCollection
    {
        return $this->repository->allExceptProcessing();
    }
}
