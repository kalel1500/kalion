@props(['id', 'labelText', 'required' => false, 'disabled' => false])

<div class="flex items-start">
    <div class="flex items-center h-5">
        <input
            id="{{ $id }}"
            aria-describedby="{{ $id }}"
            type="checkbox"
            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800"
            @disabled($disabled)
            @required($required)
        >
    </div>
    <div class="ml-3 text-sm">
        <label for="{{ $id }}" class="text-gray-500 dark:text-gray-300">
            {{ $labelText ?? $slot }}
            {{ $error ?? '' }}
        </label>
    </div>
</div>