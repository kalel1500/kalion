<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure;

use Illuminate\Support\ServiceProvider;
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
        $this->configureFortify();
        $this->configureViews();
        $this->configureAuthentication();
        $this->configureUserCreation();
    }

    /**
     * Configure Fortify settings dynamically based on kalion config.
     */
    protected function configureFortify(): void
    {
        // Set the username field from kalion config
        config(['fortify.username' => kauth()->getLoginFieldData()->name]);
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
     * Configure the custom authentication logic (supports fake login).
     */
    protected function configureAuthentication(): void
    {
        Fortify::authenticateUsing(function ($request) {
            $authenticateClass = config('kalion.auth.actions.authenticate_user');
            return app($authenticateClass)->authenticate($request);
        });
    }

    /**
     * Configure the user creation action.
     */
    protected function configureUserCreation(): void
    {
        $createUserClass = config('kalion.auth.actions.create_new_user');
        Fortify::createUsersUsing($createUserClass);
    }
}
