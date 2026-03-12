<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Http\Controllers\Controller;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Facades\AuthFlow;

class RegisterController extends Controller
{
    public function create(): View
    {
        return AuthFlow::viewRegister();
    }

    public function store(Request $request): RedirectResponse
    {
        return AuthFlow::register($request);
    }
}
