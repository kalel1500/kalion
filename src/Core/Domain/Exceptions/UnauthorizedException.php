<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Exceptions;

use Thehouseofel\Kalion\Core\Domain\Exceptions\Base\KalionHttpException;

class UnauthorizedException extends KalionHttpException
{
    const STATUS_CODE = 403;
    const SHOW_LOGOUT_FORM = true;

    public static function forRoles(string $roles): static
    {
        $message = kalion()->trans('k::auth.invalid_roles');

        if (kalion()->config()->displayRoleInException()) {
            $message .= ' ' . kalion()->trans('k::auth.necessary_roles', ['roles' => $roles]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function forPermissions(string $permissions): static
    {
        $message = kalion()->trans('k::auth.invalid_permissions');

        if (kalion()->config()->displayPermissionInException()) {
            $message .= ' ' . kalion()->trans('k::auth.necessary_permissions', ['permissions' => $permissions]);
        }

        return new static(static::STATUS_CODE, $message);
    }

    public static function missingTraitHasAbilities($user): static
    {
        $class = get_class($user);

        return new static(403, kalion()->trans('k::auth.missing_trait_has_abilities', ['class' => $class]));
    }

    public static function notLoggedIn(): static
    {
        return new static(static::STATUS_CODE, kalion()->trans('k::auth.not_logged_in'));
    }

}
