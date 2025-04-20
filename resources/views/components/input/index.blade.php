@props(['type', 'required' => false, 'disabled' => false])

<input
    type="{{ $type }}"
    name="{{ $attributes->get('id') ?? $attributes->get('name') }}"
    {{ $attributes->only('id') }}
    {{ $attributes->only('class')->mergeTailwind('bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500') }}
    {{ $attributes->except(['id', 'class']) }}
    @disabled($disabled)
    @required($required)
/>