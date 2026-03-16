@props(['value', 'for' => null])

<label for="{{ $for }}" class="block mb-2.5 text-sm font-medium text-heading">
    {{ $value ?? $slot }}
</label>
