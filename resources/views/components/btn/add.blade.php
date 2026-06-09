@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="brand" {{ $attributes }}>
    <x-fwb-s-circle-plus class="{{ twMerge('size-5', $iconClass) }}"/>
</x-kal::button-subtle>
