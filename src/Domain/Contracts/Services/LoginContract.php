<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface LoginContract
{
    public function view(?Request $request = null): View;
    public function login(Request $request): RedirectResponse;
    public function logout(Request $request): RedirectResponse;
}
