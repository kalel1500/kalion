@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="success" {{ $attributes }}>
    <x-fas-floppy-disk class="{{ twMerge('size-5', $iconClass) }}"/>
</x-kal::button-subtle>
