<?php

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Role;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

}
