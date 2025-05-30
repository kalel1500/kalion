<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Infrastructure\Facades\Auth;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return Auth::viewPasswordReset();
    }

    public function store(Request $request): RedirectResponse
    {
        return Auth::reset($request);
    }
}
