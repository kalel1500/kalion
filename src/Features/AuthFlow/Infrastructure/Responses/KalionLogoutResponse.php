<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class KalionLogoutResponse implements LogoutResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        return $request->wantsJson()
            ? response()->json(['message' => 'Logged out'])
            : redirect(app_url());
    }
}

