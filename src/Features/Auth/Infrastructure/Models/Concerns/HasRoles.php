<?php

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Permission;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\Role;

trait HasRoles
{
    use HasRelationships;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function permissions(): HasManyDeep
    {
        return $this->hasManyDeep(Permission::class, ['role_user', Role::class, 'permission_role']);
    }
}
