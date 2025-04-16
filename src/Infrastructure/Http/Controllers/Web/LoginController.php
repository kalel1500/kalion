<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Thehouseofel\Kalion\Domain\Exceptions\FeatureUnavailableException;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

final class LoginController extends Controller
{
    private string $model;

    public function __construct()
    {
        $this->model = Kalion::getClassUserModel();
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        if (config('kalion.auth.fake')) {
            return view(config('kalion.auth.blades.fake'));
        }

        throw new FeatureUnavailableException();
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        if (!config('kalion.auth.fake')) {
            throw new FeatureUnavailableException();
        }

        $field = Kalion::getLoginFieldData();

        $params = $request->validate([$field->name => 'required']);

        $user = $this->model::query()->where($field->name, $params[$field->name])->first();

        if (!$user) {
            return redirect()->back()->withErrors([$field->name => __('k::auth.user_not_found', ['field' => $field->label])]);
        }

        Auth::login($user);

        $request->session()->regenerate();

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
