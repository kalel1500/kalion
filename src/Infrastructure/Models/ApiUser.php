<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Thehouseofel\Kalion\Domain\Traits\ModelHasPermissions;

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
