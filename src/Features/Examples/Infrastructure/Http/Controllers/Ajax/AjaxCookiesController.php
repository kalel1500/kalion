<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Examples\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades\LayoutPreferences;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Http\Controllers\Controller;

final class AjaxCookiesController extends Controller
{
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        LayoutPreferences::set(urldecode($request->input('preferences')));

        return response_json(true, 'OK');
    }
}
