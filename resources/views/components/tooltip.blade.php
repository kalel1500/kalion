@props([
    'id'        => 'tooltip-' . uniqid(),
    'content'   => '',
    'style'     => 'dark', // dark | light
    'placement' => null,   // top | right | bottom | left
    'trigger'   => null,   // hover | click
    'animated'  => false,
    'arrow'     => true,
])

@php
    $styleClasses = match($style) {
        'light' => 'text-heading bg-neutral-primary-medium border border-default',
        default => 'text-white bg-dark',
    };

    $baseClasses = 'absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium rounded-base shadow-xs opacity-0 tooltip';
    $animClasses = $animated ? ' transition-opacity duration-300' : '';

    $finalClasses = $baseClasses . $animClasses . ' ' . $styleClasses;
@endphp

@if($slot)
    <span
        data-tooltip-target="{{ $id }}"
        @if($style === 'light') data-tooltip-style="light" @endif
        @if($placement) data-tooltip-placement="{{ $placement }}" @endif
        @if($trigger) data-tooltip-trigger="{{ $trigger }}" @endif
    >
        {{ $slot }}
    </span>
@endif

<div id="{{ $id }}" role="tooltip" class="{{ $finalClasses }}">
    {{ $content }}
    @if($arrow)
        <div class="tooltip-arrow" data-popper-arrow></div>
    @endif
</div>
