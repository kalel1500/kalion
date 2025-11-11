<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionHttpException;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\UserEntity;

class UnauthorizedException extends KalionHttpException
{
    const STATUS_CODE = 403;

    public static function forRoles(string $roles): static
    {
        $message = __('k::auth.invalid_roles');

        if (config('kalion.auth.display_role_in_exception')) {
            $message .= ' ' . __('k::auth.necessary_roles', ['roles' => $roles]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function forPermissions(string $permissions): static
    {
        $message = __('k::auth.invalid_permissions');

        if (config('kalion.auth.display_permission_in_exception')) {
            $message .= ' ' . __('k::auth.necessary_permissions', ['permissions' => $permissions]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function missingTraitHasPermissions(UserEntity $user): static
    {
        $class = get_class($user);

        return new static(403, __('k::auth.missing_trait_has_roles', ['class' => $class]));
    }

    public static function notLoggedIn(): static
    {
        return new static(static::STATUS_CODE, __('k::auth.not_logged_in'));
    }

}
