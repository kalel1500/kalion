<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Concerns\HasPermissions;

class ApiUser extends Authenticatable
{
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];
}
