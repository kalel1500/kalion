@props(['required' => false, 'disabled' => false])

<x-kal::input
    type="password"
    {{ $attributes }}
    :disabled="$disabled"
    :required="$required"
/>