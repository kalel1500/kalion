<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Helpers;

use Illuminate\Support\Facades\DB;
use Thehouseofel\Kalion\Domain\Traits\Singelton;

final class MyDebug
{
    use Singelton;

    public function start(): void
    {
        DB::enableQueryLog();
    }

    public function show(): void
    {
        dd(DB::getQueryLog());
    }

}
