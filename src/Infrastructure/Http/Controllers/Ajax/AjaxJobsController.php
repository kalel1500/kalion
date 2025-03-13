<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Application\GetAllFailedJobsUseCase;
use Thehouseofel\Kalion\Application\GetAllJobsUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Repositories\JobEloquentRepository;
use Throwable;

final class AjaxJobsController extends Controller
{
    public function __construct(private JobEloquentRepository $jobEloquentRepository)
    {
    }

    /**
     * @throws Throwable
     */
    public function getJobs(): JsonResponse
    {
        try {
            $getAllJobsUseCase = new GetAllJobsUseCase($this->jobEloquentRepository);
            $jobs              = $getAllJobsUseCase->__invoke();
            return responseJson(true, 'success', ['jobs' => $jobs->toArray()]);
        } catch (Throwable $th) {
            return responseJsonError($th);
        }
    }

    /**
     * @throws Throwable
     */
    public function getFailedJobs(): JsonResponse
    {
        try {
            $getAllFailedJobsUseCase = new GetAllFailedJobsUseCase($this->jobEloquentRepository);
            $jobs                    = $getAllFailedJobsUseCase->__invoke();
            return responseJson(true, 'success', ['jobs' => $jobs->toArray()]);
        } catch (Throwable $th) {
            return responseJsonError($th);
        }
    }
}
