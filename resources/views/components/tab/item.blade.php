@aware(['itemClass' => ''])
@props(['active' => false, 'href' => '#'])

@php
    $classes_common = 'inline-block p-4 border-b rounded-t-base' . ' ' . $itemClass;
    $classes_normal = 'border-transparent hover:text-fg-brand hover:border-brand';
    $classes_active = 'text-fg-brand border-brand';
    $classes_case = $active ? $classes_active : $classes_normal;
    $classes = $classes_common . ' ' . $classes_case;
@endphp

<li class="me-2">
    <a href="{{ $href }}" class="@twMerge($classes)" @if($active) aria-current="page" @endif>{{ $slot }}</a>
</li>
