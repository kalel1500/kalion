<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Flow;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Flow\Contracts\Register;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class RegisterService implements Register
{
    public function view(?Request $request = null): View
    {
        return view(config('kalion.auth.blades.register'));
    }

    public function register(Request $request): RedirectResponse
    {
        $model = kauth()->getClassUserModel();

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . $model],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required'],
        ]);

        $user = $model::query()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
