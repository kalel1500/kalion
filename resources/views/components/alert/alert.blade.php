@props([
    'variant'   => 'brand',
    'icon'      => false,
    'border'    => false,
    'list'      => false,
    'title'     => '',
    'dismiss'   => false,
    'id'        => 'alert-' . uniqid(),
])

@php
    $variantClasses = [
        'brand'     => 'text-fg-brand-strong   bg-brand-softer             ',
        'danger'    => 'text-fg-danger-strong  bg-danger-soft              ',
        'success'   => 'text-fg-success-strong bg-success-soft             ',
        'warning'   => 'text-fg-warning        bg-warning-soft             ',
        'dark'      => 'text-heading           bg-neutral-secondary-medium ',
    ];
    $classes = 'p-4 mb-4 text-sm rounded-base ' . $variantClasses[$variant];
    if ($icon || $dismiss || $list) {
        $classes .= 'flex ';
        if (! $list) {
            $classes .= 'items-start sm:items-center ';
        }
    }
    if ($border) {
        $borderClasses = [
            'brand'     => 'border-brand-subtle',
            'danger'    => 'border-danger-subtle',
            'success'   => 'border-success-subtle',
            'warning'   => 'border-warning-subtle',
            'dark'      => 'border-default-medium',
        ];
        $classes .= 'border ' . $borderClasses[$variant];
    }
    $btnDismissVariantClasses = [
        'brand'     => 'focus:ring-brand-medium   hover:bg-brand-soft',
        'danger'    => 'focus:ring-danger-medium  hover:bg-danger-medium',
        'success'   => 'focus:ring-success-medium  hover:bg-success-medium',
        'warning'   => 'focus:ring-warning-medium  hover:bg-warning-medium',
        'dark'      => 'focus:ring-neutral-tertiary  hover:bg-neutral-tertiary-medium',
    ];
    $btnDismissClasses = 'ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8 shrink-0 ' . $btnDismissVariantClasses[$variant];
@endphp

<div id="{{ $id }}" {{ $attributes->except('role')->twMerge($classes) }} role="alert">
    @if($icon)
        <svg class="w-4 h-4 me-2 shrink-0 mt-0.5 sm:mt-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
    @endif

    @if($list)
        <div class="slot">
            <span class="font-medium">{{ $title }}</span>
            <ul class="mt-2 list-disc list-outside space-y-1 ps-2.5">
                {{ $slot }}
            </ul>
        </div>
    @else
        <div class="slot">{{ $slot }}</div>
    @endif

    @if($dismiss)
        <button type="button" class="{{ $btnDismissClasses }}" data-dismiss-target="#{{ $id }}" aria-label="Close">
            <span class="sr-only">Close</span>
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
        </button>
    @endif
</div>
