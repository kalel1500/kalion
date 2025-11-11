<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Jobs\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Features\Jobs\Application\GetAllFailedJobsUseCase;
use Thehouseofel\Kalion\Features\Jobs\Application\GetAllJobsUseCase;
use Throwable;

final class AjaxJobsController extends Controller
{
    public function __construct(
        private readonly GetAllJobsUseCase       $getAllJobsUseCase,
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
