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
        'inactive'                    => 'Websockets service stopped on this machine',
        'failed_action_message'       => 'The action was performed successfully although the event was not broadcast to other users (they will need to refresh their browser)',
        'failed_action_blade_message' => 'The progress is not being reflected in real time to other users, although this will not affect the execution of the page.',
    ],
    'queues'     => [
        'active'   => 'The queue service is enabled',
        'inactive' => 'The queue service is disabled',
    ],

];
