<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Infrastructure\Facades\Auth;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return Auth::viewLogin();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        return Auth::login($request);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        return Auth::logout($request);
    }
}
