<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class KalionRegisterResponse implements RegisterResponseContract
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
            ? response()->json(['message' => 'Registered'])
            : redirect()->intended(redirect_after_login_to($request));
    }
}

