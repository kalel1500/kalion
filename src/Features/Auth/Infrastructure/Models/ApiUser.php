<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Concerns\HasAbilities;

class ApiUser extends Authenticatable
{
    use HasAbilities;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];
}
