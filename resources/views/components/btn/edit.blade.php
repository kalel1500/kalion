@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="warning" {{ $attributes }}>
    <x-heroicon-c-pencil-square class="{{ twMerge('size-5', $iconClass) }}"/>
</x-kal::button-subtle>
