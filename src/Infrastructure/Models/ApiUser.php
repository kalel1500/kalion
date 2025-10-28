<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Thehouseofel\Kalion\Infrastructure\Models\Concerns\ModelHasPermissions;

class ApiUser extends Authenticatable
{
    use ModelHasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];
}
