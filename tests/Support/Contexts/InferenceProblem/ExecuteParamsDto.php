<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\InferenceProblem;

final class ExecuteParamsDto
{
    public $users;

    public function __construct()
    {
        $this->users = new UserCollection([]);
    }
}
