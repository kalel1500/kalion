<?php

declare(strict_types=1);

namespace Src\Home\Infrastructure\Http\Controllers;

use Illuminate\Contracts\View\View;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Controller;

final class HomeController extends Controller
{
    public function home(): View
    {
        return view('pages.home.index');
    }
}
