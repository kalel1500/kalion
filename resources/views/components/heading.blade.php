@props(['type'])

@php
    $classes = 'mb-4 font-bold dark:text-white';
    $arrayBtnClasses = [
        'h1' => 'text-5xl',
        'h2' => 'text-4xl',
        'h3' => 'text-3xl',
        'h4' => 'text-2xl',
        'h5' => 'text-xl',
        'h6' => 'text-lg',
    ];
    $btnClasses = $arrayBtnClasses[$type];
@endphp

<h1 {{ $attributes->twMerge($classes.' '.$btnClasses) }}>{{ $slot }}</h1>
