<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Web;

use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Controller;

final class JobsController extends Controller
{
    public function queuedJobs(): \Illuminate\Contracts\View\View
    {
        return view('kal::pages.jobs');
    }

    public function failedJobs(): \Illuminate\Contracts\View\View
    {
        return view('kal::pages.jobs');
    }
}
