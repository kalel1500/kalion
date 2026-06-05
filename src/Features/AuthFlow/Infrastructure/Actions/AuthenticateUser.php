<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticateUser
{
    /**
     * Authenticate the user based on the request.
     * Supports "fake" mode (no password check) and "real" mode.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function authenticate(Request $request)
    {
        /** @var class-string<\Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable> $model */
        $model     = kauth()->getClassUserModel();
        $fieldName = kauth()->getLoginFieldData()->name;

        if (config('kalion.auth.fake')) {
            return $this->authenticateFake($request, $model, $fieldName);
        }

        return $this->authenticateReal($request, $model, $fieldName);
    }

    /**
     * Fake authentication: find user by field only (no password check).
     * Useful for local development to impersonate any user.
     */
    protected function authenticateFake(Request $request, string $model, string $fieldName)
    {
        $fieldValue = $request->input($fieldName);

        return $model::query()->where($fieldName, $fieldValue)->first();
    }

    /**
     * Real authentication: verify password against stored hash.
     */
    protected function authenticateReal(Request $request, string $model, string $fieldName)
    {
        $fieldValue = $request->input($fieldName);
        $password   = $request->input('password');

        $user = $model::query()->where($fieldName, $fieldValue)->first();

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }
}



