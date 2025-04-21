<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Thehouseofel\Kalion\Domain\Contracts\Services\LoginContract;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

class Login implements LoginContract
{
    protected Request $request;
    protected ?string $requestIp;
    protected string  $model;
    protected string  $fieldName;
    protected string  $fieldValue;
    protected bool    $remember;
    protected string  $throttleKey;


    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(Request $request): void
    {
        $this->request    = $request;
        $this->requestIp  = $request->ip();
        $this->model      = Kalion::getClassUserModel();
        $this->fieldName  = Kalion::getLoginFieldData()->name;
        $this->fieldValue = $request->string($this->fieldName)->toString();
        $this->remember   = $request->boolean('remember');

        $this->ensureIsNotRateLimited();

        if (config('kalion.auth.fake')) {
            $this->authenticateFake();
        } else {
            $this->authenticateReal();
        }

        RateLimiter::clear($this->throttleKey());

        $request->session()->regenerate();
    }

    protected function authenticateFake(): void
    {
        $credentials = $this->request->validate([
            $this->fieldName => 'required'
        ]);

        $user = $this->model::query()->where($this->fieldName, $credentials[$this->fieldName])->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $this->fieldName => trans('auth.failed'), // __('k::auth.user_not_found', ['field' => __($fthis->ield->label)])
            ]);
        }

        Auth::login($user, $this->remember);
    }

    protected function authenticateReal(): void
    {
        $credentials = $this->request->validate([
            $this->fieldName => 'required',
            'password'       => 'required',
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                $this->fieldName => trans('auth.failed'),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this->request));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            $this->fieldName => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(): string
    {
        return $this->throttleKey ?? Str::transliterate(Str::lower($this->fieldValue) . '|' . $this->requestIp);
    }
}
