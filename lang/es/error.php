<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Errors Language Lines
    |--------------------------------------------------------------------------
    |
    | The following lines of language contain translations of genetic errors.
    |
    */

    'feature_unavailable'                           => 'Esta funcionalidad aun no esta lista',
    'exception_message_can_not_be_empty'            => 'La excepción :exception no puede tener un mensaje vacío',
    'cant_redirect_itself'                          => 'No se puede redirigir una página a sí misma. Si configuras "redirectUsers" como "/", debes redefinir esta ruta en tu aplicación.',
    'min_value_cant_be_greater_than_max'            => 'El valor mínimo no puede ser mayor que el máximo',
    'amount_must_be_greater_than_number'            => 'La cantidad debe ser mayor a :number',
    'range_exceeds_maximum_allowed'                 => 'El rango de valores excede el máximo permitido de :max',
    'weight_key_out_of_range'                       => 'La clave de peso :number está fuera del rango permitido (:min - :max).',
    'sum_of_probabilities_cant_be_greater_than_100' => 'La suma de probabilidades personalizadas no puede ser mayor a 100%',
    'generated_distribution_empty'                  => 'La distribución generada está vacía. Verifica tus probabilidades',
    'failed_processing_$step'                       => 'Error al procesar el paso [:step]',
    'failed_executing_$step'                        => 'Error al ejecutar el paso [:step]',
    'step_not_found_$name'                          => 'No se ha encontrado ningún paso con el nombre :name',
    'multiple_steps_found_$name'                    => 'Se han encontrado varios pasos con el mismo nombre ":name". ¿Cuál quieres ejecutar?',
    'no_step_selected'                              => 'No se ha seleccionado ningún paso',
    'percentage_must_be_between_0_and_100'          => 'El porcentaje debe estar entre 0 y 100.',
];
