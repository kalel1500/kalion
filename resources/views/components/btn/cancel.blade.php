@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="neutral" {{ $attributes }}>
    <x-fas-ban class="{{ twMerge('size-5', $iconClass) }}"/>
</x-kal::button-subtle>
