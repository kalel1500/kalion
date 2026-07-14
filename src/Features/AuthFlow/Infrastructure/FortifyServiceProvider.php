<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Fortify;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Responses\KalionLoginResponse;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Responses\KalionLogoutResponse;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Responses\KalionRegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Laravel Fortify's own service provider
        $this->app->register(\Laravel\Fortify\FortifyServiceProvider::class);

        // Bind custom responses
        $this->app->singleton(LoginResponseContract::class, KalionLoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, KalionLogoutResponse::class);
        $this->app->singleton(RegisterResponseContract::class, KalionRegisterResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set the username field from kalion config
        config(['fortify.username' => kauth()->getLoginFieldData()->name]);

        $this->configureViews();
        $this->configureActions();
    }

    /**
     * Configure the views that Fortify will use.
     */
    protected function configureViews(): void
    {
        Fortify::loginView(function () {
            if (config('kalion.auth.fake')) {
                return view(config('kalion.auth.blades.fake'));
            }
            return view(config('kalion.auth.blades.login'));
        });

        Fortify::registerView(function () {
            return view(config('kalion.auth.blades.register'));
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view(config('kalion.auth.blades.forgot_password'));
        });

        Fortify::resetPasswordView(function ($request) {
            return view(config('kalion.auth.blades.reset_password'), ['request' => $request]);
        });
    }

    /**
     * Configure the actions that Fortify will use.
     */
    protected function configureActions(): void
    {
        Fortify::authenticateUsing(function ($request) {
            return app(config('kalion.auth.actions.authenticate_user'))->authenticate($request);
        });

        Fortify::createUsersUsing(config('kalion.auth.actions.create_new_user'));

        Fortify::resetUserPasswordsUsing(config('kalion.auth.actions.reset_user_password'));
    }

    /**
     * Configure the rate limiter.
     */
    public static function configureRateLimiter(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
