<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Examples\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Http\Controllers\Controller;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cookie;

final class AjaxCookiesController extends Controller
{
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $preferences = UserPreferencesDto::fromJson(urldecode($request->input('preferences')));

        Cookie::new()
            ->setPreferences($preferences)
            ->create()
            ->queue();

        return response_json(true, 'OK');
    }
}
