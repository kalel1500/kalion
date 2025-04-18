@props([
    'href' => null,
    'tag' => 'a',
    'underline' => false,
    'value' => null,
    ])

@php
    $normal_class = $underline ? 'underline' : '';
    $hoover_class = $underline ? 'hover:no-underline' : 'hover:underline';
    $extra_class = $tag !== 'a' ? 'cursor-pointer' : '';
    $classes = "font-medium text-blue-600 $normal_class dark:text-blue-500 $hoover_class $extra_class";
@endphp
<{{ $tag }} @if($tag === 'a') href="{{ $href }}" @endif {{ $attributes->mergeTailwind($classes) }}>{{ $value ?? $slot }}</{{ $tag }}>
