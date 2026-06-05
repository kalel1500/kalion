<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Actions;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     * @return mixed
     */
    public function create(array $input)
    {
        $model = kauth()->getClassUserModel();

        Validator::make($input, [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . (new $model)->getTable()],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms'    => ['required'],
        ])->validate();

        return $model::query()->create([
            'name'     => $input['name'],
            'email'    => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}

