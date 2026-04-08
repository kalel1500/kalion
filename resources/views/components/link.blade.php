@props([
    'tag'       => 'a',
    'underline' => false,
    'text'      => null,
    'title'     => null,
    'type'      => 'default', // default, button, card
    'icon'      => null,      // arrow, external
])

@php
    $baseClasses = match($type) {
        'button' => 'inline-flex items-center text-white bg-brand box-border border border-transparent hover:bg-brand-strong focus:ring-4 focus:ring-brand-medium shadow-xs font-medium leading-5 rounded-base text-sm px-4 py-2.5 focus:outline-none',
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

<{{ $tag }} {{ $attributes->twMerge($finalClasses) }}>
    @if($type === 'card')
        @if($title)
            <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">{{ $title }}</h5>
        @endif
        <p class="text-body">{{ $text ?? $slot }}</p>
    @else
        {{ $text ?? $slot }}

        @if($icon === 'arrow')
            <svg class="{{ $iconClasses }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4"/>
            </svg>
        @elseif($icon === 'external')
            <svg class="{{ $iconExternalClasses }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 14v4.833A1.166 1.166 0 0 1 16.833 20H5.167A1.167 1.167 0 0 1 4 18.833V7.167A1.166 1.166 0 0 1 5.167 6h4.618m4.447-2H20v5.768m-7.889 2.121 7.778-7.778"/>
            </svg>
        @endif
    @endif
</{{ $tag }}>
