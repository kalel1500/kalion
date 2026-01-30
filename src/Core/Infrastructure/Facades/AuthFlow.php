<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Facades;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\AuthenticationFlow as AuthenticationFlowContract;

/**
 * @method static View viewLogin(Request $request = null)
 * @method static RedirectResponse login(Request $request)
 * @method static RedirectResponse logout(Request $request)
 *
 * @method static View viewRegister(Request $request = null)
 * @method static RedirectResponse register(Request $request)
 *
 * @method static View viewPasswordReset(Request $request = null)
 * @method static RedirectResponse reset(Request $request)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\AuthenticationFlowService
 */
final class AuthFlow extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AuthenticationFlowContract::class;
    }
}
