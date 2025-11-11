<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\Auth;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Controller;

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
