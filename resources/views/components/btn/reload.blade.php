@props([
    'iconClass' => '',
])

<x-kal::button-subtle color="brand" {{ $attributes }}>
    <x-fas-arrows-rotate class="{{ twMerge('size-5', $iconClass) }}"/>
{{--    <x-fwb-o-rotate  class="{{ twMerge('size-5', $iconClass) }}"/>--}}
</x-kal::button-subtle>
