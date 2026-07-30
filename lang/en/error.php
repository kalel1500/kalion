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

    'feature_unavailable'                           => 'This feature is not ready yet',
    'exception_message_can_not_be_empty'            => 'The exception :exception cannot have an empty message',
    'cant_redirect_itself'                          => 'A page cannot redirect to itself. If you configure "redirectUsers" as "/", you must redefine this route in your application.',
    'min_value_cant_be_greater_than_max'            => 'The minimum value cannot be greater than the maximum',
    'amount_must_be_greater_than_number'            => 'The amount must be greater than :number',
    'range_exceeds_maximum_allowed'                 => 'The range of values exceeds the maximum allowed value of :max',
    'weight_key_out_of_range'                       => 'The weight key :number is outside the allowed range (:min - :max).',
    'sum_of_probabilities_cant_be_greater_than_100' => 'The sum of custom probabilities cannot be greater than 100%',
    'generated_distribution_empty'                  => 'The generated distribution is empty. Check your probabilities.',
    'failed_processing_$step'                       => 'Error processing step [:step]',
    'failed_executing_$step'                        => 'Error executing step [:step]',
    'step_not_found_$name'                          => 'No step was found with the name :name',
    'multiple_steps_found_$name'                    => 'Multiple steps were found with the same name ":name". Which one do you want to execute?',
    'no_step_selected'                              => 'No step has been selected',
    'percentage_must_be_between_0_and_100'          => 'The percentage must be between 0 and 100.',
];
