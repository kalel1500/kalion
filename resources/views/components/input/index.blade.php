@props(['type', 'required' => false, 'disabled' => false])

@php
    $name = $attributes->get('id') ?? $attributes->get('name');
    $messages = $errors->get($name);

    $ringClasses = 'focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-500 dark:focus:border-blue-500';
    $errorClasses = 'border-red-500 focus:ring-red-500 focus:border-red-500 dark:border-red-500';

    $other = 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white';
    $final = $other.' '.($messages ? $errorClasses : $ringClasses);
@endphp

<input
    type="{{ $type }}"
    name="{{ $name }}"
    {{ $attributes->only('id') }}
    {{ $attributes->only('class')->mergeTailwind($final) }}
    {{ $attributes->except(['id', 'class']) }}
    @disabled($disabled)
    @required($required)
/>