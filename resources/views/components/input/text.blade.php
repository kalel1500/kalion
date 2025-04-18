@props(['required' => false, 'disabled' => false])

<x-kal::input
    type="text"
    {{ $attributes }}
    :disabled="$disabled"
    :required="$required"
/>