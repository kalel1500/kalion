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

    'invalid_roles'                 => 'No tienes permisos para acceder a esta página', // El usuario no tiene los roles correctos
    'invalid_permissions'           => 'No tienes permisos para acceder a esta página', // El usuario no tiene los permisos correctos
    'necessary_roles'               => 'Los roles necesarios son :roles',
    'necessary_permissions'         => 'Los permisos necesarios son :permissions',
    'missing_trait_has_permissions' => 'La Entidad :class debe usar el trait Thehouseofel\Kalion\Domain\Traits\EntityHasPermissions',
    'not_logged_in'                 => 'El usuario no esta logueado',
    'user_not_found'                => 'No existe ningún usuario con ese :field', // No encontramos ninguna cuenta con ese ":field"
    'register'         => [
        'title'         => 'Registro',
        'card_title'    => 'Crear una cuenta',
        'btn'           => 'Crear una cuenta',
        'question'      => '¿Ya tienes una cuenta?',
        'question_link' => 'Inicia sesión aquí',
    ],
    'login'            => [
        'title'          => 'Iniciar sesión',
        'card_title'     => 'Inicia sesión en tu cuenta',
        'btn'            => 'Iniciar sesión',
        'question'       => '¿Aún no tienes una cuenta?',
        'question_link'  => 'Registrate',
        'password_reset' => '¿Has olvidado tu contraseña?',
    ],
    'password_reset'   => [
        'title'      => 'Contraseña olvidada',
        'card_title' => '¿Olvidaste tu contraseña?',
        'card_text'  => '¡No te preocupes! Solo escribe tu correo electrónico y te enviaremos un código para restablecer tu contraseña.',
        'btn'        => 'Restablecer contraseña',
    ],

];
