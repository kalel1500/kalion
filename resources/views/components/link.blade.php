@props([
    'tag'       => 'a',
    'underline' => false,
    'text'      => null,
    'title'     => null,
    'type'      => 'default', // default, button, card
    'icon'      => null,      // arrow, external

    // Default btn props
    'variant'   => 'brand',
    'size'      => 'base',
])

@php
    // Clases para cuando NO es un botón
    $baseClasses = match($type) {
        'card'   => 'bg-neutral-primary-soft block max-w-sm p-6 border border-default rounded-base shadow-xs hover:bg-neutral-secondary-medium',
        default  => 'inline-flex items-center font-medium text-fg-brand',
    };

    $underlineClasses = ($type === 'default')
        ? ($underline ? 'underline hover:no-underline' : 'hover:underline')
        : '';

    $finalClasses = "$baseClasses $underlineClasses";

    $isButton = $type === 'button';
    $iconClasses = $isButton
        ? 'w-4 h-4 ms-1.5 rtl:rotate-180 -me-0.5'
        : 'w-5 h-5 ms-1 rtl:rotate-180';

    $externalRotation = 'rtl:rotate-[270deg]';
    $iconExternalClasses = $isButton
        ? "w-4 h-4 ms-1.5 $externalRotation -me-0.5"
        : "w-4 h-4 ms-2 $externalRotation";
@endphp

@if($isButton)
    {{--
        Si es tipo botón, llamamos al componente x-button.
        Pasamos la etiqueta (tag) para que x-button sepa si renderizar <button> o <a>
        y mezclamos la clase inline-flex para mantener la estructura de los iconos.
    --}}
    <x-button
        :tag="$tag"
        :variant="$variant"
        :size="$size"
        {{ $attributes->twMerge('inline-flex items-center') }}
    >
        {{ $text ?? $slot }}
        <x-kal::partials.link-icons :icon="$icon" :icon-classes="$iconClasses"/>
    </x-button>
@else
    <{{ $tag }} {{ $attributes->twMerge($finalClasses) }}>
        @if($type === 'card')
            @if($title)
                <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">{{ $title }}</h5>
            @endif
            <p class="text-body">{{ $text ?? $slot }}</p>
        @else
            {{ $text ?? $slot }}
            <x-kal::partials.link-icons/>
        @endif
    </{{ $tag }}>
@endif
