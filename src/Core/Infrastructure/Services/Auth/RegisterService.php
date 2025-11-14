<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Auth\Contracts\Register;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\User;

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
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required'],
        ]);

        $user = User::query()->create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
