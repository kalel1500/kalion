<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\CookiePreferencesDo;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Services\CookieService;

final class AjaxCookiesController extends Controller
{
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $preferences = CookiePreferencesDo::fromJson(urldecode($request->input('preferences')));

        CookieService::new()
            ->setPreferences($preferences)
            ->create()
            ->queue();

        return responseJson(true, 'OK');
    }
}
