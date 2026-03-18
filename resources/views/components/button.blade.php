@props(['type' => 'button', 'color' => 'default', 'size' => 'base'])

@php
    $classes = 'border focus:ring-4 font-medium leading-5 rounded-base focus:outline-none';
    $sizeClasses = [
        'xs'    => 'px-3 py-2   text-xs  ',
        'sm'    => 'px-3 py-2   text-sm  ',
        'base'  => 'px-4 py-2.5 text-sm  ',
        'lg'    => 'px-5 py-3   text-base',
        'lx'    => 'px-6 py-3.5 text-base',
    ];
    $colorClasses = [
        'default'   => 'text-white      bg-brand                    box-border  border-transparent      hover:bg-brand-strong                                   focus:ring-brand-medium             shadow-xs',
        'secondary' => 'text-body       bg-neutral-secondary-medium box-border  border-default-medium   hover:bg-neutral-tertiary-medium    hover:text-heading  focus:ring-neutral-tertiary         shadow-xs',
        'tertiary'  => 'text-body       bg-neutral-primary-soft                 border-default          hover:bg-neutral-secondary-medium   hover:text-heading  focus:ring-neutral-tertiary-soft    shadow-xs',
        'success'   => 'text-white      bg-success                  box-border  border-transparent      hover:bg-success-strong                                 focus:ring-success-medium           shadow-xs',
        'danger'    => 'text-white      bg-danger                   box-border  border-transparent      hover:bg-danger-strong                                  focus:ring-danger-medium            shadow-xs',
        'warning'   => 'text-white      bg-warning                  box-border  border-transparent      hover:bg-warning-strong                                 focus:ring-warning-medium           shadow-xs',
        'dark'      => 'text-white      bg-dark                     box-border  border-transparent      hover:bg-dark-strong                                    focus:ring-neutral-tertiary         shadow-xs',
        'ghost'     => 'text-heading    bg-transparent              box-border  border-transparent      hover:bg-neutral-secondary-medium                       focus:ring-neutral-tertiary                  ',
    ];
    $finalClasses = $classes.' '.$sizeClasses[$size].' '.$colorClasses[$color];
@endphp

<button type="{{ $type }}" {{ $attributes->twMerge($finalClasses) }}>{{ $slot }}</button>
