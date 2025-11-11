<?php

namespace Thehouseofel\Kalion\Infrastructure\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Infrastructure\Models\Role;

trait ModelHasPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

}
