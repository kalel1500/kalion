<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Flow\Contracts;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface Register
{
    public function view(?Request $request = null): View;

    public function register(Request $request): RedirectResponse;
}
