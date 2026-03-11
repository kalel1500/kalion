<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Facades\AuthFlow;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return AuthFlow::viewPasswordReset();
    }

    public function store(Request $request): RedirectResponse
    {
        return AuthFlow::reset($request);
    }
}
