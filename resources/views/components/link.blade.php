@props([
    'href' => null,
    'tag' => 'a',
    'underline' => false,
    ])

@php
    $normal_class = $underline ? 'underline' : '';
    $hoover_class = $underline ? 'hover:no-underline' : 'hover:underline';
    $extra_class = $tag !== 'a' ? 'cursor-pointer' : '';
    $classes = "text-blue-600 $normal_class dark:text-blue-500 $hoover_class $extra_class";
@endphp
<{{ $tag }} @if($tag === 'a') href="{{ $href }}" @endif {{ $attributes->mergeTailwind($classes) }}>{{ $slot }}</{{ $tag }}>
