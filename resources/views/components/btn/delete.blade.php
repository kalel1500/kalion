@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="danger" {{ $attributes }}>
    <x-heroicon-s-trash class="{{ twMerge('size-5', $iconClass) }}"/>
</x-kal::button-subtle>
