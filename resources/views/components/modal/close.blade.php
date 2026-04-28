@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'modalId',
    'text'          => 'Close modal',
    'forPopup'      => false,
    'declarative'   => false,
])

@php
    $nameAttrCancel = $declarative ? 'data-modal-hide' : 'data-fmodal-cancel';
    $attrCancel     = $nameAttrCancel.'='.$modalId.'';
@endphp

<button type="button" {{ $attrCancel }} class="{{ $forPopup ? 'absolute top-3 inset-e-2.5' : '' }} text-body bg-transparent hover:bg-neutral-tertiary hover:text-heading rounded-base text-sm w-9 h-9 ms-auto inline-flex justify-center items-center z-10">
    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6"/></svg>
    <span class="sr-only">{{ $text }}</span>
</button>
