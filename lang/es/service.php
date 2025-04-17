<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Services Language Lines
    |--------------------------------------------------------------------------
    |
    | The following lines of language contain translations of the
    | application's services.
    |
    */

    'websockets' => [
        'inactive'                    => 'Servicio websockets parado en esta máquina',
        'failed_action_message'       => 'La acción se ha realizado correctamente aunque el evento no se ha transmitido a otros usuarios (tendrán que actualizar su navegador)',
        'failed_action_blade_message' => 'El avance no se esta reflejando en tiempo real a otros usuarios, aunque esto no afectará a la ejecución de le página',
    ],
    'queues'     => [
        'active'   => 'El servicio de colas esta activado',
        'inactive' => 'El servicio de colas esta desactivado',
    ],

];
