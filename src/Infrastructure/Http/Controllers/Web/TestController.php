<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class TestController extends Controller
{
    public function testVitePackage(): \Illuminate\Contracts\View\View
    {
        return view('kal::pages.tests.test-vite-package');
    }

    public function sessions()
    {
        if (!debug_is_active()) {
            throw new NotFoundHttpException();
        }
        $sessions = DB::table('sessions')->get();
        foreach ($sessions as $session) {
            $decoded = base64_decode($session->payload);
            $array = unserialize($decoded);
            dump($array);
        }
    }
}
