<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Exceptions;

use Thehouseofel\Kalion\Domain\Exceptions\Base\BasicHttpException;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;

class UnauthorizedException extends BasicHttpException
{
    const STATUS_CODE = 403;

    public static function forRoles(string $roles): static
    {
        $message = __('h::auth.invalid_roles');

        if (config('kalion_auth.display_role_in_exception')) {
            $message .= ' '.__('h::auth.necessary_roles', ['roles' => $roles]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function forPermissions(string $permissions): static
    {
        $message = __('h::auth.invalid_permissions');

        if (config('kalion_auth.display_permission_in_exception')) {
            $message .= ' '.__('h::auth.necessary_permissions', ['permissions' => $permissions]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function missingTraitHasPermissions(UserEntity $user): static
    {
        $class = get_class($user);

        return new static(403, __('h::auth.missing_trait_has_roles', ['class' => $class]));
    }

    public static function notLoggedIn(): static
    {
        return new static(static::STATUS_CODE, __('h::auth.not_logged_in'));
    }

}
