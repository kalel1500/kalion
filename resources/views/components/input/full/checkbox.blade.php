@props(['labelText', 'required' => false, 'disabled' => false])

@php
    $name = $attributes->get('id') ?? $attributes->get('name');

    $classes = 'w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800';
@endphp

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input
            type="checkbox"
            name="{{ $name }}"
            {{ $attributes->only('id') }}
            aria-describedby="{{ $name }}"
            {{ $attributes->only('class')->mergeTailwind($classes) }}
            {{ $attributes->except(['id', 'class']) }}
            @disabled($disabled)
            @required($required)
        >
    </div>
    <div class="ml-3 text-sm">
        <label for="{{ $name }}" class="text-gray-500 dark:text-gray-300">
            {{ $labelText ?? $slot }}
            {{ $error ?? '' }}
        </label>
    </div>
</div>