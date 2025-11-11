<?php

namespace Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\Role;

trait ModelHasPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

}
