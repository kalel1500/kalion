<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thehouseofel\Kalion\Domain\Exceptions\FeatureUnavailableException;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class AuthController extends Controller
{
    private string $model;

    public function __construct()
    {
        $this->model = get_class_user_model();
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (config('kalion_auth.login.fake')) {
            return view('kal::pages.login.fake');
        }

        throw new FeatureUnavailableException();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!config('kalion_auth.login.fake')) {
            throw new FeatureUnavailableException();
        }

        $params = $request->validate(['email' => 'required']);
        $user = $this->model::query()->where('email', $params['email'])->first();
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'El email existe']);
        }

        Auth::login($user);
        return redirect()->intended('/dashboard'); // Redirige a donde corresponda
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
