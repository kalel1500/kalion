<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thehouseofel\Kalion\Infrastructure\Facades\Auth as Login;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (config('kalion.auth.fake')) {
            return view(config('kalion.auth.blades.fake'));
        }

        return view(config('kalion.auth.blades.login'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        Login::authenticate($request);
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(app_url());
    }
}
