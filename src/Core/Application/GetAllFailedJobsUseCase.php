<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Application;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Collections\FailedJobCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\JobRepository;

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
