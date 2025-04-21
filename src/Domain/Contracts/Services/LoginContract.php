<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

use Illuminate\Http\Request;

interface LoginContract
{
    public function authenticate(Request $request): void;
}
