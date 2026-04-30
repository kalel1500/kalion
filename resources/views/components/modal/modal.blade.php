@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'id',
    'class'             => null,
    'static'            => false,
    'type'              => 'default',   // default | popup | form
    'icon'              => null,        // info | success | warn | error (solo popup)
    'size'              => 'medium',    // small | medium | large | extralarge
    'placement'         => null,        // top-left | top-right | bottom-left | bottom-right
    'header'            => null,
    'closeText'         => __('Close modal'),
    'footer'            => null,
    'showConfirmButton' => true,
    'showCancelButton'  => true,
    'showDenyButton'    => false,
    'confirmText'       => null,
    'cancelText'        => null,
    'denyText'          => null,
    'confirmVariant'    => 'brand',     // brand | secondary | tertiary | success | danger | warning | dark | ghost
    'cancelVariant'     => 'secondary', // brand | secondary | tertiary | success | danger | warning | dark | ghost
    'denyVariant'       => 'danger',    // brand | secondary | tertiary | success | danger | warning | dark | ghost
    'declarative'       => false,
    'showSpinner'       => false,
])

@php
    $headerIsSlot = $header instanceof \Illuminate\View\ComponentSlot;
    $footerIsSlot = $footer instanceof \Illuminate\View\ComponentSlot;

    // Textos por defecto según tipo
    $confirmText ??= $type === 'popup' ? __("Yes, I'm sure") : __('Accept');
    $cancelText  ??= $type === 'popup' ? __('No, cancel')    : __('Cancel');
    $denyText    ??= __('Decline');

    // Tamaños (ignorados en popup y form, que usan max-w-md fijo)
    $sizeClasses = [
        'small'      => 'max-w-md',
        'medium'     => 'max-w-2xl',
        'large'      => 'max-w-4xl',
        'extralarge' => 'max-w-7xl',
    ];
    $maxW = match($type) {
        'popup', 'form' => 'max-w-md',
        default         => $sizeClasses[$size] ?? 'max-w-2xl',
    };

    // Placement
    $placementAttr = match($placement) {
        'top-left', 'top-right', 'bottom-left', 'bottom-right' => $placement,
        default => null,
    };

    // Icono popup
    $iconName = match($icon) {
        'success' => 'ri-checkbox-circle-line', // fwb-o-check-circle
        'warn'    => 'ri-error-warning-line', // fwb-o-exclamation-circle
        'error'   => 'ri-close-circle-line', // fwb-o-x-circle
        default   => 'ri-information-line', // fwb-o-info-circle
    };

    $confirmVariant = $type === 'popup' ? 'danger' : 'brand';
@endphp

<div
    id="{{ $id }}"
    @if($static) data-modal-backdrop="static" @endif
    @if($placementAttr) data-modal-placement="{{ $placementAttr }}" @endif
    tabindex="-1"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
>
    <div class="relative p-4 w-full {{ $maxW }} max-h-full">
        <!-- Modal content -->
        <div {{ $attributes->except('role')->twMerge('relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6 ' . $class) }}>

            <!-- Spinner -->
            @php($spinnerClass = 'fmodal-spinner ' . ($showSpinner ? 'block' : 'hidden'))
            <x-kal::spinner overlay :class="$spinnerClass" :backdrop-class="$spinnerClass"/>

            @if($type === 'popup')
                {{-- ═══════════════════════════════════════════ --}}
                {{-- POPUP                                       --}}
                {{-- ═══════════════════════════════════════════ --}}
                <x-kal::modal.close :modalId="$id" :text="$closeText" :forPopup="true" :declarative="$declarative"/>
                <div class="p-4 md:p-5 text-center">
                    @if($icon)
                        <span class="mx-auto mb-4 text-fg-disabled w-12 h-12 flex items-center justify-center [&>svg]:w-12 [&>svg]:h-12">
                            @svg($iconName)
                        </span>
                    @endif
                    <div class="mb-6 text-body">{{ $slot }}</div>
                    @if(! $footerIsSlot)
                        <div class="flex items-center space-x-4 justify-center">
                            <x-kal::modal.buttons
                                :modal-id="$id"
                                :declarative="$declarative"
                                :show-confirm-button="$showConfirmButton"
                                :show-cancel-button="$showCancelButton"
                                :show-deny-button="$showDenyButton"
                                :confirm-text="$confirmText"
                                :cancel-text="$cancelText"
                                :deny-text="$denyText"
                                :confirm-variant="$confirmVariant"
                                :cancel-variant="$cancelVariant"
                                :deny-variant="$denyVariant"
                            />
                        </div>
                    @else
                        {{ $footer }}
                    @endif
                </div>

            @else
                <!-- Modal header -->
                @if($headerIsSlot)
                    {{ $header }}
                @else
                    <div class="border-b border-default pb-4 md:pb-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-heading">{{ $header }}</h3>
                            <x-kal::modal.close :modalId="$id" :text="$closeText" :declarative="$declarative"/>
                        </div>
                        <x-kal::alert variant="brand"   class="hidden fmodal-message-info" :dismiss="true"/>
                        <x-kal::alert variant="success" class="hidden fmodal-message-success" :dismiss="true"/>
                        <x-kal::alert variant="danger"  class="hidden fmodal-message-error" :dismiss="true"/>
                        <x-kal::alert variant="warning" class="hidden fmodal-message-warning" :dismiss="true"/>
                    </div>
                @endif

                <!-- Modal body -->
                @if($type === 'form')
                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- FORM                                        --}}
                    {{-- ═══════════════════════════════════════════ --}}

                    {{-- El slot es el <form> completo con body + footer propios --}}
                    {{ $slot }}

                @else
                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- DEFAULT (+ static, sizes, placement)        --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div class="fmodal-body space-y-4 md:space-y-6 py-4 md:py-6">
                        {{ $slot }}
                    </div>

                    <!-- Modal footer -->
                    @if($footerIsSlot)
                        {{ $footer }}
                    @else
                        <div class="flex items-center border-t border-default space-x-4 pt-4 md:pt-5">
                            <x-kal::modal.buttons
                                :modal-id="$id"
                                :declarative="$declarative"
                                :show-confirm-button="$showConfirmButton"
                                :show-cancel-button="$showCancelButton"
                                :show-deny-button="$showDenyButton"
                                :confirm-text="$confirmText"
                                :cancel-text="$cancelText"
                                :deny-text="$denyText"
                                :confirm-variant="$confirmVariant"
                                :cancel-variant="$cancelVariant"
                                :deny-variant="$denyVariant"
                            />
                        </div>
                    @endif
                @endif
            @endif

        </div>
    </div>
</div>
