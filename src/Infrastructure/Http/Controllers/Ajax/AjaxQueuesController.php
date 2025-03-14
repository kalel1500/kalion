<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Domain\Exceptions\ServiceException;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Events\EventCheckQueuesStatus;
use Thehouseofel\Kalion\Infrastructure\Services\QueueService;
use Thehouseofel\Kalion\Infrastructure\Services\WebsocketsService;
use Throwable;

final class AjaxQueuesController extends Controller
{
    /**
     * @throws Throwable
     */
    public function checkService(): JsonResponse
    {
        try {
            QueueService::check(__('k::service.queues.inactive'));
            $response = response_json(true, __('k::service.queues.active'));
        } catch (ServiceException $e) {
            $response = response_json(false, $e->getMessage());
        } catch (Throwable $e) {
            $response = response_json_error($e, false);
        }
        return WebsocketsService::emitEvent($response, new EventCheckQueuesStatus($response));
    }
}
