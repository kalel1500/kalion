<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Thehouseofel\Kalion\Core\Infrastructure\Models\Role;

trait ModelHasPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

}
