@props(['for'])

@php($messages = $errors->get($for))

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1 text-sm text-red-600 dark:text-red-500 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif