<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\FailedJobCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\JobCollection;
use Thehouseofel\Kalion\Infrastructure\Models\FailedJob;
use Thehouseofel\Kalion\Infrastructure\Models\Jobs;

class EloquentJobRepository implements JobRepositoryContract
{
    protected string $model = Jobs::class;
    protected string $modelFailedJobs = FailedJob::class;

    public function allExceptProcessing(): JobCollection
    {
        $eloquentResult = $this->model::query()->whereNull('reserved_at')->get();
        return JobCollection::fromArray($eloquentResult->toArray());
    }

    public function deleteAllExceptThoseNotInProcess(): void
    {
        // $first = $this->eloquentModel::query()->first();
        // $this->eloquentModel::query()->where('id', '!=', optional($first)->id)->delete();
        $this->model::query()->whereNull('reserved_at')->delete();
    }

    public function allFailed(): FailedJobCollection
    {
        $eloquentResult = $this->modelFailedJobs::query()->orderBy('failed_at', 'desc')->get();
        return FailedJobCollection::fromArray($eloquentResult->toArray());
    }
}
