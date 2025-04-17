<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\Auth;

use Illuminate\Contracts\View\View;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class RegisterController extends Controller
{
    public function create(): View
    {
        return view(config('kalion.auth.blades.register'));
    }

}
