<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

final class KalionController extends Controller
{
    public function root()
    {
        return redirect(appUrl());
    }

    public function testVitePackage(): \Illuminate\Contracts\View\View
    {
        return view('kal::pages.tests.test-vite-package');
    }

    public function sessions()
    {
        if (!debugIsActive()) {
            throw new NotFoundHttpException('The route sessions could not be found.');
        }
        $sessions = DB::table('sessions')->get();
        foreach ($sessions as $session) {
            $decoded = base64_decode($session->payload);
            $array = unserialize($decoded);
            dump($array);
        }
    }
}
