<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Application\GetAllFailedJobsUseCase;
use Thehouseofel\Kalion\Application\GetAllJobsUseCase;
use Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepositoryContract;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Throwable;

final class AjaxJobsController extends Controller
{
    public function __construct(private JobRepositoryContract $repositoryJob)
    {
    }

    /**
     * @throws Throwable
     */
    public function getJobs(): JsonResponse
    {
        try {
            $getAllJobsUseCase = new GetAllJobsUseCase($this->repositoryJob);
            $jobs              = $getAllJobsUseCase->__invoke();
            return response_json(true, 'success', ['jobs' => $jobs->toArray()]);
        } catch (Throwable $th) {
            return response_json_error($th);
        }
    }

    /**
     * @throws Throwable
     */
    public function getFailedJobs(): JsonResponse
    {
        try {
            $getAllFailedJobsUseCase = new GetAllFailedJobsUseCase($this->repositoryJob);
            $jobs                    = $getAllFailedJobsUseCase->__invoke();
            return response_json(true, 'success', ['jobs' => $jobs->toArray()]);
        } catch (Throwable $th) {
            return response_json_error($th);
        }
    }
}
