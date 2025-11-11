<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Application;

use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\JobRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\JobCollection;

final class GetAllJobsUseCase
{
    public function __construct(private JobRepository $repository)
    {
    }

    public function __invoke(): JobCollection
    {
        return $this->repository->allExceptProcessing();
    }
}
