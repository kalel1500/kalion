@props(['place' => 'start'])

@php
    $classes = 'flex mb-5';
    if ($place === 'end') {
        $classes .= ' justify-end';
    }
@endphp

<nav aria-label="Breadcrumb" {{ $attributes->twMerge($classes) }}>
    <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
        {{ $slot }}
    </ol>
</nav>
