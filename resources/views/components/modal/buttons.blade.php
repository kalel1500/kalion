@php /** @var \Illuminate\View\ComponentSlot $slot */ @endphp
@php /** @var \Illuminate\View\ComponentAttributeBag $attributes */ @endphp
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp

@props([
    'modalId',
    'declarative',
    'confirmVariant',
    'confirmText',
    'cancelText',
])

{{-- Confirm --}}
<x-kal::button
    :data-modal-hide="$declarative ? $modalId : null"
    :data-fmodal-confirm="!$declarative ? $modalId : null"
    variant="{{ $confirmVariant }}"
>
    {{ $confirmText }}
</x-kal::button>

{{-- Cancel --}}
<x-kal::button
    :data-modal-hide="$declarative ? $modalId : null"
    :data-fmodal-cancel="!$declarative ? $modalId : null"
    variant="secondary"
>{{ $cancelText }}</x-kal::button>
