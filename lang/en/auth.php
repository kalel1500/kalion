<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auth Language Lines
    |--------------------------------------------------------------------------
    |
    | The following lines of language contain auth translations.
    |
    */

    'invalid_roles'                 => 'You do not have permission to access this page', // User does not have the right roles
    'invalid_permissions'           => 'You do not have permission to access this page', // User does not have the right permissions
    'necessary_roles'               => 'Necessary roles are :roles',
    'necessary_permissions'         => 'Necessary permissions are :permissions',
    'missing_trait_has_permissions' => 'Entity class :class must use Thehouseofel\Kalion\Domain\Traits\EntityHasPermissions trait.',
    'not_logged_in'                 => 'User is not logged in',
    'user_not_found'                => 'There is no user with that :field',
    'register'         => [
        'title'         => 'Register',
        'card_title'    => 'Create an account',
        'btn'           => 'Create an account',
        'question'      => 'Already have an account?',
        'question_link' => 'Login here',
    ],
    'login'            => [
        'title'          => 'Login',
        'card_title'     => 'Sign in to your account',
        'btn'            => 'Sign in',
        'question'       => 'Don’t have an account yet?',
        'question_link'  => 'Sign up',
        'password_reset' => 'Forgot password?',
    ],
    'password_reset'   => [
        'title'      => 'Forgot password',
        'card_title' => 'Forgot your password?',
        'card_text'  => 'Don\'t fret! Just type in your email and we will send you a code to reset your password!',
        'btn'        => 'Reset password',
    ],

];
