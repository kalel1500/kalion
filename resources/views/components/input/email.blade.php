@props(['required' => false, 'disabled' => false])

<x-kal::input
    type="email"
    {{ $attributes }}
    :disabled="$disabled"
    :required="$required"
/>