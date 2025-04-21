<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface PasswordResetContract
{
    public function view(?Request $request = null): View;
    public function reset(Request $request): RedirectResponse;
}
