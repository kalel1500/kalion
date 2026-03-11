<?php

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Laravel\Models\Role;

trait HasPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

}
