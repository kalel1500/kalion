<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Facades\AuthFlow;
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
