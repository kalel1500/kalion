@props([
    'tag'     => 'button',
    'type'    => 'button',
    'tone'    => 'subtle',  // subtle | tinted
    'color'   => 'neutral', // warning | danger | success | brand | neutral
    'size'    => 'md',      // sm | md | lg
    'rounded' => 'md',
])

@php
    $sizeClasses = [
        'sm' => 'p-1',
        'md' => 'p-1.5',
        'lg' => 'p-2',
    ];
    $colorClasses = [
        'subtle' => [
            'warning' => 'text-body-subtle hover:text-fg-warning hover:bg-warning-soft',
            'danger'  => 'text-body-subtle hover:text-fg-danger  hover:bg-danger-soft',
            'success' => 'text-body-subtle hover:text-fg-success hover:bg-success-soft',
            'brand'   => 'text-body-subtle hover:text-fg-brand   hover:bg-brand-soft',
            'neutral' => 'text-body-subtle hover:text-heading    hover:bg-neutral-secondary-medium',
        ],
        'tinted' => [
            'warning' => 'text-orange-700  hover:text-orange-900        hover:bg-warning-soft                dark:text-[#dd753b]   dark:hover:text-orange-300',
            'danger'  => 'text-fg-danger   hover:text-fg-danger-strong  hover:bg-danger-soft',
            'success' => 'text-fg-success  hover:text-fg-success-strong hover:bg-success-soft',
            'brand'   => 'text-fg-brand    hover:text-fg-brand-strong   hover:bg-brand-soft',
            'neutral' => 'text-body        hover:text-heading           hover:bg-neutral-secondary-medium',
        ],
    ];
    $finalClasses = 'transition-colors focus:outline-none'
        .' '.get_rounded_class($rounded)
        .' '.$sizeClasses[$size]
        .' '.$colorClasses[$tone][$color];
@endphp

<{{ $tag }} {{ $tag === 'button' ? "type=$type" : '' }} {{ $attributes->twMerge($finalClasses) }}>{{ $slot }}</{{ $tag }}>
