@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'id',
    'class'         => null,
    'static'        => false,
    'header'        => null,
    'closeText'     => __('Close modal'),
    'footer'        => null,
    'confirmText'   => __('Accept'),
    'cancelText'    => __('Decline'),
])

@php
    $headerIsSlot = $header instanceof \Illuminate\View\ComponentSlot;
    $footerIsSlot = $footer instanceof \Illuminate\View\ComponentSlot;
@endphp

<!-- Main modal -->
<div id="{{ $id }}" @if($static) data-modal-backdrop="static" @endif tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="{{ twMerge('relative bg-neutral-primary-soft border border-default rounded-base shadow-sm p-4 md:p-6', $class) }}">
            <!-- Modal header -->
            @if($headerIsSlot)
                {{ $header }}
            @else
                <div class="flex items-center justify-between border-b border-default pb-4 md:pb-5">
                    <h3 class="text-lg font-medium text-heading">
                        {{ $header }}
                    </h3>
                    <button type="button" class="text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center" data-modal-hide="static-modal">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
                        <span class="sr-only">{{ $closeText }}</span>
                    </button>
                </div>
            @endif

            <!-- Modal body -->
            <div class="space-y-4 md:space-y-6 py-4 md:py-6">
                {{ $slot }}
            </div>

            <!-- Modal footer -->
            @if($footerIsSlot)
                {{ $footer }}
            @else
                <div class="flex items-center border-t border-default space-x-4 pt-4 md:pt-5">
                    <x-kal::button data-modal-hide="{{ $id }}" variant="brand">{{ $confirmText }}</x-kal::button>
                    <x-kal::button data-modal-hide="{{ $id }}" variant="secondary">{{ $cancelText }}</x-kal::button>
                </div>
            @endif

        </div>
    </div>
</div>
