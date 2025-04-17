<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Application\GetAllFailedJobsUseCase;
use Thehouseofel\Kalion\Application\GetAllJobsUseCase;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Throwable;

final class AjaxJobsController extends Controller
{
    public function __construct(
        private readonly GetAllJobsUseCase $getAllJobsUseCase,
        private readonly GetAllFailedJobsUseCase $getAllFailedJobsUseCase,
    )
    {
    }

    public function getJobs(): JsonResponse
    {
        try {
            $jobs = $this->getAllJobsUseCase->__invoke();
            return response_json(true, 'success', $jobs);
        } catch (Throwable $th) {
            return response_json_error($th);
        }
    }

    public function getFailedJobs(): JsonResponse
    {
        try {
            $jobs = $this->getAllFailedJobsUseCase->__invoke();
            return response_json(true, 'success', $jobs);
        } catch (Throwable $th) {
            return response_json_error($th);
        }
    }
}
