@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'modalId',
    'text' => null,
])

<x-kal::button data-modal-target="{{ $modalId }}" data-modal-toggle="{{ $modalId }}" {{ $attributes }}>{{ $text ?? $slot }}</x-kal::button>
