@props([
    'variant' => 'brand',
    'icon'    => false,
    'border'  => false,
    'title'   => null,
    'dismiss' => false,
    'btnId'   => null,
    'id'      => 'alert-' . uniqid(),
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

    $bntDismissVariantClasses = [
        'brand'     => 'focus:ring-brand-medium     hover:bg-brand-soft',
        'danger'    => 'focus:ring-danger-medium    hover:bg-danger-medium',
        'success'   => 'focus:ring-success-medium   hover:bg-success-medium',
        'warning'   => 'focus:ring-warning-medium   hover:bg-warning-medium',
        'dark'      => 'focus:ring-neutral-tertiary hover:bg-neutral-tertiary-medium',
    ];
    $bntDismissClasses = 'ms-auto -mx-1.5 -my-1.5 rounded focus:ring-2 p-1.5 inline-flex items-center justify-center h-8 w-8 shrink-0 ' . $bntDismissVariantClasses[$variant];

    $bntVariantClasses = [
        'brand'     => 'bg-brand     hover:bg-brand-strong   focus:ring-brand-medium',
        'danger'    => 'bg-danger    hover:bg-danger-strong  focus:ring-danger-medium',
        'success'   => 'bg-success   hover:bg-success-strong focus:ring-success-medium',
        'warning'   => 'bg-warning   hover:bg-warning-strong focus:ring-warning-medium',
        'dark'      => 'bg-dark-soft hover:bg-dark-strong    focus:ring-neutral-tertiary',
    ];
    $bntClasses = 'inline-flex items-center text-white box-border border border-transparent focus:ring-4 shadow-xs font-medium leading-5 rounded-base text-xs px-3 py-1.5 focus:outline-none ' . $bntVariantClasses[$variant];
@endphp

<div id="{{ $id }}" {{ $attributes->except('role')->twMerge($classes) }} role="alert">
    <div class="flex items-center justify-between">
        @if($title)
            <div class="flex items-center">
                @if($icon)
                    <svg class="w-4 h-4 shrink-0 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11h2v5m-2 0h4m-2.592-8.5h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                @endif
                <h3 class="font-medium">{{ $title }}</h3>
            </div>
        @endif

        @if($dismiss)
            <button type="button" data-dismiss-target="#{{ $id }}" aria-label="Close" class="{{ $bntDismissClasses }}">
                <span class="sr-only">Close</span>
                <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
            </button>
        @endif
    </div>
    <div class="mt-2 mb-4  text-fg-warning-strong">
        {{ $slot }}
    </div>
    @if($btnId)
        <button type="button" class="{{ $bntClasses }}" id="{{ $btnId }}">
            <svg class="w-3.5 h-3.5 me-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/><path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>
            {{ __('View more') }}
        </button>
    @endif
</div>
