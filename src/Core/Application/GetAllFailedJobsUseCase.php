<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Application;

use Thehouseofel\Kalion\Core\Domain\Contracts\Repositories\JobRepository;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Collections\FailedJobCollection;

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
